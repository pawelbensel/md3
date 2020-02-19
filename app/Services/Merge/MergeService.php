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

    public function mergre(Similar $similar): array
    {
        $mergeHistoryArray = [];
        /** @var OneManyModel $similarObject */
        $similarObject  = $similar->similar;
        $similarObject->loadAllHasMany();
        $similarObject->loadAllBelongsToMany();

        foreach($similarObject->getRelations() as $relationCollection)
        {
            foreach($relationCollection as $singleModel){
                dd($singleModel);
                /** @var Model $copyModel */
                $copyModel = $singleModel->replicate();
                // get foregin key based on ClassName ex: prop_id, agent_id
                $foreginKey = strtolower(StringHelpers::getUntilSecondCappitalLetter(class_basename($copyModel))).'_id';
                if($singleModel instanceof Agent){
                    dd($foreginKey);
                }
                if($singleModel instanceof Prop){
                    dd($foreginKey);
                }
                $copyModel->$foreginKey = $similar->object->id;
                $copyModel->matching_rate = $similar->matching_rate;
                $copyModel->matched_by = $similar->matched_by;
                $copyModel->save();
                $singleModel->delete();

                $mergeHistory = new MergeHistory();
                $mergeHistory->similar_id = $similar->id;
                $mergeHistory->target_id = $copyModel->id;
                $mergeHistory->previous_id = $singleModel->id;
                $mergeHistory->target_type = get_class($copyModel);
                $mergeHistory->previous_type = get_class($singleModel);
                $mergeHistory->save();
                array_push($mergeHistoryArray, $mergeHistory);
            }
        }
        if($similar->similar instanceof Office){
            $agents = $similar->similar->agents()->get();
            foreach ($agents as $agent){
                $agent->offices()->sync($similar->object);
            }
            $props = $similar->similar->props()->get();
            foreach ($props as $prop){
                $prop->offices()->sync($similar->object);
            }
        }


        $similarObject->delete();
        $similar->delete();

        return $mergeHistoryArray;
    }

    public function revert(Similar $similar)
    {
        $similar->similar->restore();
        foreach ($similar->mergeHistory as $singleMergeHistory){
                $singleMergeHistory->previous->restore();
                $singleMergeHistory->target->delete();
                $singleMergeHistory->delete();
        }
        $similar->restore();
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