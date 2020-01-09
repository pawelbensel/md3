<?php

namespace App\Services;
use App\Models\SourceRow;

class SourceRowService extends BaseSourceHashService{   

    public function create(string $row, string $hash)
    {                   
        return SourceRow::create(['source'=> $this->source, 'row' => $row, 'hash' => $hash]);
    }

    public function getByHash(string $hash)
    {
        return SourceRow::where('hash',$hash)->first();
    }

    public static function markParsed($id) {
    	$sourceObject = SourceRow::find($id);
    	$sourceObject->parsed = true;
    	$sourceObject->save();
    }

} 

