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
    protected $mlsName;
    protected $cleanups = [
                    'inc'
                ];

    public function __construct()
    {

    }

    private function cleanUp($string) {

        foreach ($this->cleanups as $clean) {
            $string = str_replace($clean,'',$string);
        }
            return $string;
    }

    public function setMlsName(string $mlsName)
    {
        $this->mlsName  = $mlsName;
    }

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


    private function getByName2() {
        $name = StringHelpers::escapeLike($this->checkedRow['office_name']);
        $nameSlug = StringHelpers::slug($this->checkedRow['office_name']);
        $cleanNameSlug = $this->cleanUp($nameSlug);
        $address1 = StringHelpers::escapeLike($this->checkedRow['address1']);
        $address2 = (array_key_exists('address2', $this->checkedRow))?
                StringHelpers::escapeLike($this->checkedRow['address2'])
                :'';
        $this->checkedRow['address2'] = $address2;
        $city = StringHelpers::escapeLike($this->checkedRow['city']);
        $phone = StringHelpers::cleanupPhoneNumber($this->checkedRow['office_phone']);
        $shortPhoneNumbers = StringHelpers::shortPhoneNumber($cleanNameSlug);

        $officeQueryBase = Office::
            leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('office_addresses', 'offices.id', '=', 'office_addresses.office_id')
            ->leftJoin('office_phones', 'offices.id', '=', 'office_phones.office_id')
            ->leftJoin('office_zips', 'offices.id', '=', 'office_zips.office_id')
            ->leftJoin('office_mls_ids', 'offices.id', '=', 'office_mls_ids.office_id');


        $this->matched_by = 'office_name, address1, address2, city, phone';
        $this->matching_rate = 100;
        $this->log('Try to get by'. $this->matched_by);
        $officeQuery = clone $officeQueryBase;
        $officeQuery
                ->select("offices.id");
        if(!empty($name)) {
            $officeQuery->whereRaw("office_names.name like '%$name%'");
        }
        if(!empty($address1)) {
            $officeQuery->whereRaw("office_addresses.address1 like '%$address1%'");
        }
        if(!empty($address2)){
            $officeQuery->whereRaw("office_addresses.address2 like '%$address2%'");
        }
        if(!empty($city)){
            $officeQuery->whereRaw("office_addresses.city like '%$city%'");
        }
        if(!empty($phone)){
            $officeQuery->whereRaw("office_phones.slug like '%$phone%'");
        }

        $office = $officeQuery->first();

        if (!$office) {

            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'office_name,address1, address2, city,';
            $this->matching_rate = 98;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")
                ->whereRaw("office_addresses.address1 like '%$address1%'")
                ->whereRaw("((office_addresses.address2 like '%$address2%') or (office_addresses.address2 is null))")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
        }

        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'office_name, city, phone';
            $this->matching_rate = 95;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.name like '%$name%'")
                ->whereRaw("office_addresses.address1 like '%$address1%'")
                ->whereRaw("((office_addresses.address2 like '%$address2%') or (office_addresses.address2 is null))")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
        }

        if (!$office) {

            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'slug office_name, address1, address2, city';
            $this->matching_rate = 90;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$nameSlug%'")
                ->whereRaw("office_addresses.address1 like '%$address1%'")
                ->whereRaw("((office_addresses.address2 like '%$address2%') or (office_addresses.address2 is null))")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
        }

        if (!$office) {

            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'slug office_name, city, phone';
            $this->matching_rate = 90;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$nameSlug%'")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->whereRaw("office_phones.slug like '%$phone%'")
                ->first();
        }

        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'clean slug office_name, address1, address2, city';
            $this->matching_rate = 85;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$cleanNameSlug%'")
                ->whereRaw("office_addresses.address1 like '%$address1%'")
                ->whereRaw("((office_addresses.address2 like '%$address2%') or (office_addresses.address2 is null))")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
        }

        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'clean slug office_name, city, phone';
            $this->matching_rate = 85;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$cleanNameSlug%'")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->whereRaw("office_phones.slug like '%$phone%'")
                ->first();
        }

        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'clean slug office_name, phone';
            $this->matching_rate = 80;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$cleanNameSlug%'")
                ->whereRaw("office_phones.slug like '%$phone%'")
                ->first();
        }

        if (!$office) {
            if ($shortPhoneNumbers) {
                $officeQuery = clone $officeQueryBase;
                $this->matched_by = 'clean slug office_name, short phone';
                $this->matching_rate = 75;
                $this->log('Try to get by'. $this->matched_by);
                foreach ($shortPhoneNumbers as $shortNumber)
                 {
                    $officeQuery->orWhereRaw("office_phones.slug like '%$shortNumber%'");
                 }

                $office = $officeQuery
                    ->select("offices.id")
                    ->whereRaw("office_names.slug like '%$cleanNameSlug%'")
                    ->whereRaw("office_phones.slug like '%$phone%'")
                    ->first();
            }

        }

        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'clean slug office_name, city';
            $this->matching_rate = 80;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("office_names.slug like '%$cleanNameSlug%'")
                ->whereRaw("office_addresses.city like '%$city%'")
                ->first();
        }

/*
        if (!$office) {
            $officeQuery = clone $officeQueryBase;
            $this->matched_by = 'soudex office_name';
            $this->matching_rate = 80;
            $this->log('Try to get by'. $this->matched_by);

            $office = $officeQuery
                ->select("offices.id")
                ->whereRaw("levenshtein_ratio('".\Str::slug($name,'')."', office_names.slug) >80")
                //->whereRaw("office_addresses.city like '%?%'", [$city])
            ->first();
        }
*/
        $this->log('Check If was found');
        //dd($office->toArray());
        return $office;
    }

    private function updateName() {
    	$exist = false;
    	foreach ($this->office->names as $name) {
    		if (
                ($name->name == $this->checkedRow['office_name'])&&
                ($name->source == $this->source)
                )
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
    		if  (
                ($name->company_name == $this->checkedRow['company_name']) &&
                ($name->source == $this->source)
                )
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
                ($address->city == $this->checkedRow['city'])&&
                ($address->source == $this->source))
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
            if (
                ($phone->phone == $this->checkedRow['office_phone'])&&
                ($phone->source == $this->source)
               )
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

    private function addMlsId() {
        if (isset($this->checkedRow['mls_id'])) {
            $relObject = new OfficeMlsId();
            $relObject->mls_id = $this->checkedRow['mls_id'];
            $relObject->mls_name = $this->mlsName;
            $relObject->source = $this->source;
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            $this->office->mlsIds()->save($relObject);
        }
    }

    private function addPhone() {

        if (isset($this->checkedRow['office_phone'])){

            $relObject = new OfficePhone;
            $relObject->phone = $this->checkedRow['office_phone'];
            $relObject->source = $this->source;
            $relObject->slug = StringHelpers::cleanupPhoneNumber($relObject->phone);
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
        $this->addMlsId();
        $this->addZip();
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
