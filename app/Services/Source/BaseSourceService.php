<?php

namespace App\Services\Source;

class BaseSourceService
{
    
    protected $sourceName;
    protected $mapArray;
        
    public function setSourceName()
    {
        echo "WTF GET";    
    }

    public function map($row){
        
        foreach($this->mapArray as $objectName => $objectMap) {
            foreach ($objectMap as $destinationName => $sourceName) {
                if (isset($row->$sourceName) && ($row->$sourceName<>'')) {
                    $returnArray[$objectName][$destinationName] = $row->$sourceName;
                }
            }
        }

        return $returnArray;
    }
   
}
