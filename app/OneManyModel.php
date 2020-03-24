<?php

namespace App;

use App\Traits\Historable;
use DDZobov\PivotSoftDeletes\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;

class OneManyModel extends Model
{
    use
        SoftDeletes,
        Historable,
        HasRelationships;

    protected $dates = ['deleted_at'];

    public function loadAllHasMany(bool $trashed = false)
    {
        if(!$trashed){
            $this->load($this->getAllRelations('HasMany'));
            return;
        }

        foreach ($this->getAllRelations('HasMany') as $relation){
            $this->load([ $relation => function ($query){
                $query->withTrashed();
            }]);
        }
    }

    public function loadAllBelongsToMany(bool $trashed = false)
    {
        if(!$trashed){
            $this->load($this->getAllRelations('BelongsToMany'));
            return;
        }

        foreach ($this->getAllRelations('BelongsToMany') as $relation){
            $this->load([ $relation => function ($query){
                $query->withTrashed();
            }]);
        }
    }

    public static function getAllRelations(string $type)
    {
        $relations = [];
        $reflextionClass = new ReflectionClass(get_called_class());

        foreach($reflextionClass->getMethods() as $method)
        {
            $doc = $method->getDocComment();
            if($doc && strpos($doc, $type) !== false)
            {
                $relations[] = (strpos(strtolower($method->getName()), strtolower($type)) === false)?
                    $method->getName(): null;
            }
        }

        return array_filter($relations);
    }
}
