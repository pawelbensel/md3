<?php

namespace App\Services;

use App\Helpers\StringHelpers;
use Illuminate\Support\Facades\DB;

use App\Models\Office;
use App\Models\OfficeAddress;
use App\Models\OfficeCity;
use App\Models\OfficeCompanyName;
use App\Models\OfficeEmail;
use App\Models\OfficeMlsId;
use App\Models\OfficeMsaId;
use App\Models\OfficeName;
use App\Models\OfficePhone;
use App\Models\OfficeState;
use App\Models\OfficeZip;




class OfficeService extends BaseService
{
    
    protected $office;
    protected $sourceObjectId;
    protected $sourceRowId;
    
    public function setSourceRowId($sourceRowId) {
        $this->sourceRowId = $sourceRowId;
    }

    private function log($string){
        if (is_string($string)) {
            echo $string."\n\r";
        } else {
            dump($string);
        }

    }

    private function getByMsaId() {
		
		if (isset($this->checkedRow['msa_id'])) {
			
			$msaId = $this->checkedRow['msa_id'];
			
			$office = Office::whereHas('msaIds', 
				function ($msaIds) use ($msaId) {
	            	$msaIds->where([
	               		'office_msa_ids.msa_id' => $msaId
	            	]);
	        	})
	        ->with('names')
	        ->first();        
	        
	        return $office;        
	       }

	    return null;
    }

    private function getByName() {
		        
        $this->matched_by = 'office_name,address1, address2, city';
        $this->matching_rate = 100;

			$name = $this->checkedRow['office_name'];
            $address1 = $this->checkedRow['address1'];
            $address2 = $this->checkedRow['address2'];
            $city = $this->checkedRow['city'];
            $this->log($this->checkedRow);
            $this->log('Try to get by'. $this->matched_by);
			$office = Office::whereHas('names', 
				function ($names) use ($name) {
	            	$names->where([
	                	'office_names.name' => $name
	            ]);
	        })->whereHas('addresses', 
                function ($address) use ($address1, $address2, $city) {
                    $address->where([
                        'office_addresses.address1' => $address1,
                        'office_addresses.address2' => $address2,
                        'office_addresses.city' => $city,
                ]);
            })
	        ->with('names')
            ->with('addresses')
	        ->first();
	       
           if (!$office) {
                $this->matched_by = 'office_name, city';
                $this->matching_rate = 90;
                $this->log('Try to get by'. $this->matched_by);
                $office = Office::whereHas('names', 
                    function ($names) use ($name) {
                        $names->where([
                            'office_names.name' => $name
                    ]);
                })->whereHas('addresses', 
                    function ($address) use ($address1, $address2, $city) {
                        $address->where([
                            'office_addresses.city' => $city,
                    ]);
                })
                ->with('names')
                ->with('addresses')
                ->first();
            }

            if (!$office) {
                $this->matched_by = 'office_name';
                $this->log('Try to get by'. $this->matched_by);
                $this->matching_rate = 80;
                $office = Office::whereHas('names', 
                    function ($names) use ($name) {
                        $names->where([
                            'office_names.name' => $name
                    ]);
                })->whereHas('addresses', 
                    function ($address) use ($address1, $address2, $city) {
                        $address->where([
                            'office_addresses.city' => $city,
                    ]);
                })
                ->with('names')
                ->with('addresses')
                ->first();
            }

        return $office;
    }


    private function getByName2() {

        $this->matched_by = 'office_name,address1, address2, city';
        $this->matching_rate = 100;

        $name = StringHelpers::escapeLike($this->checkedRow['office_name']);
        $address1 = StringHelpers::escapeLike($this->checkedRow['address1']);
        $address2 = StringHelpers::escapeLike($this->checkedRow['address2']);
        $city = StringHelpers::escapeLike($this->checkedRow['city']);
        //$this->log($this->checkedRow);
        $officeQueryBase = Office::
            leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('office_addresses', 'offices.id', '=', 'office_addresses.office_id');

        $this->log('Try to get by'. $this->matched_by);
        $officeQuery = clone $officeQueryBase;
        $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")
                ->whereRaw("office_addresses.address1 like '%$address1%'")
                ->whereRaw("office_addresses.address2 like '%$address2%'")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
                        

        if (!$office) {
            $this->log('Try to get by'. $this->matched_by);
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'office_name, city';
            $this->matching_rate = 90;            
            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")
                ->whereRaw("office_addresses.city like '%$city%'")
                //Probably state will be needed here
                //->whereRaw("office_addresses.city like '%?%'", [$city])                
            ->first();
        }

        if (!$office) {            
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'slug office_name';
            $this->matching_rate = 80;            
            $this->log('Try to get by'. $this->matched_by);
            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")            
                //->whereRaw("office_addresses.city like '%?%'", [$city])                
            ->first();            
        }

        if (!$office) {            
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'slug office_name';
            $this->matching_rate = 80;            
            $this->log('Try to get by'. $this->matched_by);
            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")
                //->whereRaw("office_addresses.city like '%?%'", [$city])                
            ->first();            
        }

        if (!$office) {
            $this->log('Try to get by'. $this->matched_by);
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'soudex office_name';
            $this->matching_rate = 80;            
            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("levenshtein_ratio('".\Str::slug($name,'')."', office_names.slug) >80")
                //->whereRaw("office_addresses.city like '%?%'", [$city])                
            ->first();            
        }

        $this->log('Check If was found');
        //dd($office->toArray());
        return $office;
    }

    private function updateName() {
    	$exist = false;    	
    	foreach ($this->office->names as $name) {			
    		if ($name->name == $this->checkedRow['office_name'])
    		{
    			$exist = true;
    		}    		
    	}

    	if (!$exist) {
    		$this->addName();
    	}
    }

    private function updateCompanyName() {
    	$exist = false;    	
    	foreach ($this->office->companyNames as $name) {			
    		if ($name->company_name == $this->checkedRow['company_name'])
    		{
    			$exist = true;
    		}    		
    	}

    	if (!$exist) {
    		$this->addCompanyName();
    	}
    }

    private function updateAddress() {
    	$exist = false;    	
    	foreach ($this->office->addresses as $address) {            
    		if (
                ($address->address1 == $this->checkedRow['address1']) && 
                ($address->address2 == $this->checkedRow['address2']) && 
                ($address->city == $this->checkedRow['city']))
    		{
    			$exist = true;
    		}    		
    	}

    	if (!$exist) {
    		$this->addAddress();
    	}
    }


    private function updatePhone() {

        echo "update phone ".$this->checkedRow['office_phone'];
        $exist = false;     
        foreach ($this->office->phones as $phone) {            
            if ($phone->phone == $this->checkedRow['office_phone'])
            {
                $exist = true;
            }           
        }

        if (!$exist) {
            $this->addPhone();
        }
    }

    private function update() {
    	$this->updateName();
    	$this->updateCompanyName();
    	$this->updateAddress();
        $this->updatePhone();
    }

    private function addMsaId() {
    	if (isset($this->checkedRow['msa_id'])){
    		$relObject = new OfficeMsaId;
    		$relObject->msa_id = $this->checkedRow['msa_id'];
            $relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source;

    		$this->office->msaIds()->save($relObject);
    	}
    }

	private function addName() {
    	if (isset($this->checkedRow['office_name'])){
    		$relObject = new OfficeName;
    		$relObject->name = $this->checkedRow['office_name'];
    		$relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            
    		$this->office->names()->save($relObject);
    	}
    }

	private function addCompanyName() {	

		if (isset($this->checkedRow['company_name'])){
            echo "ADD PHONE";
    		$relObject = new OfficeCompanyName;
    		$relObject->company_name = $this->checkedRow['company_name'];
            $relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            
    		$this->office->companyNames()->save($relObject);
    	}
    }

    private function addPhone() { 

        if (isset($this->checkedRow['office_phone'])){

            $relObject = new OfficePhone;
            $relObject->phone = $this->checkedRow['office_phone'];                    
            $relObject->source = $this->source;
            $relObject->source_row_id = $this->sourceRowId;            
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;            
            $this->office->phones()->save($relObject);
        }
    }

    private function addZip() { 

        if (isset($this->checkedRow['zip'])){
            $relObject = new OfficeZip;
            $relObject->zip = $this->checkedRow['zip'];                    
            $relObject->source = $this->source;
            $relObject->source_row_id = $this->sourceRowId;            
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;            
            $this->office->zips()->save($relObject);
        }
    }
	
    private function addAddress() {	
		if (isset($this->checkedRow['address1']) || isset($this->checkedRow['address2'])||isset($this->checkedRow['city']))
		{
    		$relObject = new OfficeAddress;
    		if (isset($this->checkedRow['address1'])) {
    			$relObject->address1 = $this->checkedRow['address1'];
    		
       		}

    		if (isset($this->checkedRow['address2'])) {
    			$relObject->address2 = $this->checkedRow['address2'];    	
    		}

            if (isset($this->checkedRow['city'])) {
                $relObject->city = $this->checkedRow['city'];                
            }

    		$relObject->source = $this->source;
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            
    		$this->office->addresses()->save($relObject);
    	}
	}

    private function create() {	    	
    	$this->office = Office::create(['source' => $this->source]);
        $this->matching_rate = 100;
        $this->matched_by = null;
    	$this->addMsaId();
    	$this->addName();
    	$this->addCompanyName();
    	$this->addAddress();
        $this->addZip();
        $this->addPhone();
         //state
        $this->log('Add the office');
        $this->log($this->office->name);
        $this->log($this->office->id);
	   	return $this->office;
    }

    public function match() {
		        
		if ($office = $this->getByName2()) 
    		return $office;

    	$office = $this->create();

    	return $office;

    }

    public function getId($row) {

        	$this->checkedRow = $row;
            $this->sourceObjectId = $row['source_object']['source_object_id'];
        	$this->office = $this->match();            
        	   if (!$this->office->wasRecentlyCreated) {        		
                    $this->update();        	
            }            
            return $this->office->id;

    	
    }


}
