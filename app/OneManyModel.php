<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class OneManyModel extends Model
{
    public function loadAllHasMany(bool $trashed = false)
    {
        if(!$trashed){
            $this->load($this->getAllHasManyUserRelations());
            return;
        }

        $this->load(array_map(function($relation){
            return [$relation => function($query){
               $query->withTrashed();
            }];
        },$this->getAllHasManyUserRelations()));
    }

    public static function getAllHasManyUserRelations()
    {
        $relations = [];
        $reflextionClass = new ReflectionClass(get_called_class());

        foreach($reflextionClass->getMethods() as $method)
        {
            $doc = $method->getDocComment();
            if($doc && strpos($doc, 'HasMany') !== false)
            {
                $relations[] = (strpos(strtolower($method->getName()), 'hasmany') === false)?
                    $method->getName(): null;
            }
        }

        return array_filter($relations);
    }
}
