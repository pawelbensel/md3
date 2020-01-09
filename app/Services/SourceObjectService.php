<?php

namespace App\Services;
use App\Models\SourceObject;

class SourceObjectService extends BaseSourceHashService {   

    public function create(string $row, string $hash, string $type )
    {                   
        return SourceObject::create(['source'=> $this->source, 'object' => $row, 'object_type' => $type, 'hash' => $hash]);
    }

    public function getByHash(string $hash, string $object_type)
    {
        return SourceObject::where('hash',$hash)->where('object_type', $object_type)->first();
    }
    
    public static function markParsed($id) {
    	$sourceObject = SourceObject::find($id);
    	$sourceObject->parsed = true;
    	$sourceObject->save();
    }

} 

