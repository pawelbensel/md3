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

        foreach ($this->getAllHasManyUserRelations() as $relation){
            $this->load([ $relation => function ($query){
                $query->withTrashed();
            }]);
        }
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
