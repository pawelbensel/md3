<?php

namespace App\Services;
use App\Models\Office;
use App\Models\OfficeMsaId;
use App\Models\OfficeName;
use App\Models\OfficePhone;
use App\Models\OfficeState;
use App\Models\OfficeZip;
//use App\Models\OfficeMsaId;

class OfficeService extends BaseService
{
    protected $sourceName;
    protected $mapArray;

    
    private function getByMsaId($msaId) {
		$office = Office::whereHas('msaIds', function ($msaIds) use ($msaId) {            
            $msaIds->where([
                'office_msa_ids.msa_id' => $msaId
            ]);
        })->first();
        
        return $office;


    }

    private function create($row) {	    	
    	$office = Office::create(['source'=>$this->source]);
    	
    	if (isset($row['msa_id'])){
    		$relOblect = new OfficeMsaId;
    		$relOblect->msa_id = $row['msa_id'];
    		$relOblect->source = $this->source;
    		$office->msaIds()->save($relOblect);
    	}




    	return $office;
    }

    public function match($row) {
		if (isset($row['msa_id'])) {
    		if ($office = $this->getByMsaId($row['msa_id'])) 
    			return $office->id;
		}
    	
    	$office = $this->create($row);

    	return $office->id;

    }

    public function getId($row) {
    	return $this->match($row);
    }


}