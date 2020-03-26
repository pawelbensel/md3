<?php

namespace App\Services\Merge;

use App\Helpers\StringHelpers;
use App\Models\Agent;
use App\Models\MergeHistory;
use App\Models\Office;
use App\Models\Similar;
use App\OneManyModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MergeService implements MergeServiceInterface
{

    public function mergre(Similar $similar): OneManyModel
    {

        /** @var OneManyModel $similarObject */
        $similarObject  = $similar->similar;
        $similarObject->loadAllHasMany();
        $similarObject->loadAllBelongsToMany();

        foreach($similarObject->getRelations() as $relationCollection)
        {
            foreach($relationCollection as $singleModel){
                $mergeHistory = new MergeHistory();
                $mergeHistory->similar_id = $similar->id;

                if($singleModel instanceof OneManyModel){
                    $relation = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($singleModel))).'s';
                    $similarObject->$relation()->detach([$singleModel->id]);

                    $foreginKey = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($singleModel))).'_id';

                    // Relation already exists. Object has exact reltion before merge.
                    if(count($relationAlreadyExists = $similar->object->$relation()->where([$foreginKey => $singleModel->id])->get())> 0 ){
                        $similar->object->$relation()->withSoftDeletes()->detach($relationAlreadyExists);
                    };

                    $similar->object->$relation()->attach([$singleModel->id]);

                    $mergeHistory->target_id = $singleModel->id;
                    $mergeHistory->previous_id = $singleModel->id;
                    $mergeHistory->target_type = get_class($singleModel);
                    $mergeHistory->previous_type = get_class($singleModel);
                }else {
                    /** @var Model $copyModel */
                    $copyModel = $singleModel->replicate();
                    // get foregin key based on ClassName ex: prop_id, agent_id
                    $foreginKey = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($copyModel))).'_id';
                    $copyModel->$foreginKey = $similar->object->id;
                    $copyModel->matching_rate = $similar->matching_rate;
                    $copyModel->matched_by = $similar->matched_by;
                    $copyModel->save();
                    $singleModel->delete();

                    $mergeHistory->target_id = $copyModel->id;
                    $mergeHistory->previous_id = $singleModel->id;
                    $mergeHistory->target_type = get_class($copyModel);
                    $mergeHistory->previous_type = get_class($singleModel);
                }

                $mergeHistory->save();
            }
        }
        $similarObject->delete();
        $similar->delete();

        return $similar->object;
    }

    public function revert(Similar $similar): OneManyModel
    {
        $similar->similar->restore();
        foreach ($similar->mergeHistory as $singleMergeHistory){
            if($singleMergeHistory->previous instanceof OneManyModel &&
                $singleMergeHistory->target instanceof OneManyModel){
                $relation = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($singleMergeHistory->previous))).'s';
                $foreginKey = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($singleMergeHistory->previous))).'_id';

                $similar->object->$relation()->wherePivot('deleted_at', '=', null)->detach(['id' => $singleMergeHistory->previous->id]);
                $similar->similar->$relation()->attach([$singleMergeHistory->previous->id]);
                $similar->object->$relation()->withSoftDeletes()->wherePivot($foreginKey, '=', $singleMergeHistory->previous->id)->restore();
            } else {
                $singleMergeHistory->target->forceDelete();
                $singleMergeHistory->previous->restore();
            }
            $singleMergeHistory->delete();
        }
        $similar->restore();

        return $similar->object;
    }


    public function discard(Similar $similar)
    {
        $similar->delete();
    }

    public function getSimilars(int $pageNumber): Collection
    {

        $similars = Similar::whereNotIn('similar_id', function ($q){
            $q->select('object_id')->distinct()->from('similars')->get();})
            ->orderBy('object_id', 'DESC')->take(10)->skip(10*$pageNumber)->get();

        return $similars;
    }

    public function getHistory(int $pageNumber): Collection
    {
        $similars = Similar::onlyTrashed()
            ->orderBy('deleted_at', 'DESC')
            ->take(10)
            ->skip(10*$pageNumber)
            ->get();

        return $similars;
    }
}
