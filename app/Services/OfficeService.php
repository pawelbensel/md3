<?php

namespace App\Services;

use App\Helpers\StringHelpers;
use App\Services\Matcher\MatcherInterface;
use App\Services\Source\RetsSourceService;
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
use Illuminate\Support\Facades\File;


class OfficeService extends BaseService implements ParseServiceInterface
{

    protected $office;
    protected $sourceObjectId;
    protected $sourceRowId;

    public function __construct()
    {
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

    private function search() {
        $office = null;
        $row = $this->getPreparedRow();
        $files = File::allFiles( app_path('Services/Matcher/Matchers'));
        foreach($files as $file) {
            $class = '\\App\\Services\\Matcher\\Matchers\\'.$file->getBasename('.php');
            /** @var MatcherInterface $matcher */
            $matcher = new $class();
            if (!$matcher->supports($this)){
                continue;
            }
            $office = $matcher->match($row);
            if($office) {
                $this->matching_rate = $matcher->getRate();
                $this->matched_by = $matcher->getMatchedBy();
                break;
            }

        }

        return $office;
    }

    private function getPreparedRow(): array
    {
        $sqlArray['name'] = array_key_exists('office_name',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['office_name']): null;
        $sqlArray['name_slug'] = array_key_exists('office_name',$this->checkedRow)? StringHelpers::slug($this->checkedRow['office_name']): null;
        $sqlArray['clean_name_slug'] = (isset($sqlArray['name_slug']))? StringHelpers::cleanupSlug($sqlArray['name_slug']): null;
        $sqlArray['address1'] = array_key_exists('address1', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['address1']): null;
        $sqlArray['address2'] = array_key_exists('address2', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['address2']): null;
        $sqlArray['city'] = array_key_exists('city',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['city']): null;
        $sqlArray['phone'] = array_key_exists('phone',$this->checkedRow)? StringHelpers::cleanupPhoneNumber($this->checkedRow['office_phone']): null;
        $sqlArray['short_phone_numbers'] = (isset($sqlArray['phone']))? StringHelpers::shortPhoneNumber($sqlArray['phone']): null;

        if($this->source instanceof RetsSourceService){
            $sqlArray['mls_name'] = $this->source->getMlsName();
        }

        return $sqlArray;
    }

    private function updateName() {
    	$exist = false;
    	foreach ($this->office->names as $name) {

            if (
                ($name->name == $this->checkedRow['office_name'])&&
                ($name->source == $this->source->getSourceString())
                )
    		{
    			$exist = true;
                //$name->addPassed();
    		}
            //$name->addChecked();
            //$name->save();
    	}

    	if (!$exist) {
    		$this->addName();
    	}
    }

    private function updateCompanyName() {
    	$exist = false;

        if(!isset($this->checkedRow['company_name'])) {

            return true;
        }

    	foreach ($this->office->companyNames as $name) {
    		if  (
                ($name->company_name == $this->checkedRow['company_name']) &&
                ($name->source == $this->source->getSourceString())
                )
    		{
    			$exist = true;
                //$name->addPassed();
    		}
            //$name->addChecked();
            //$name->save();
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
                ($address->address2 == (array_key_exists('address2', $this->checkedRow)?$this->checkedRow['address2']: null)) &&
                ($address->city == $this->checkedRow['city'])&&
                ($address->source == $this->source->getSourceString()))
    		{
    			$exist = true;
                //$address->addPassed();
    		}
    		//$address->addChecked();
    		//$address->save();
    	}

    	if (!$exist) {
    		$this->addAddress();
    	}
    }

    private function updateMsaId() {
        $exist = false;
        if(!isset($this->checkedRow['msa_id'])) {

            return true;
        }

        foreach ($this->office->msaIds as $msaId) {
            if (
                ($msaId->msa_id == $this->checkedRow['msa_id']) &&
                ($msaId->source == $this->source->getSourceString()))
            {
                $exist = true;
                //$msaId->addPassed();
            }
            //$msaId->addChecked();
            //$msaId->save();
        }

        if (!$exist) {
            $this->addMsaId();
        }
    }

    private function updateMlsId() {
        $exist = false;

        if(!isset($this->checkedRow['mls_id'])) {
            return true;
        }

        foreach ($this->office->mlsIds as $mlsId) {
            if (
                ($mlsId->mls_id == $this->checkedRow['mls_id']) &&
                ($mlsId->source == $this->source->getSourceString()))
            {
                $exist = true;
                //$mlsId->addPassed();
            }
            //$mlsId->addChecked();
            //$mlsId->save();
        }

        if (!$exist) {
            $this->addMlsId();
        }
    }

    private function updatePhone() {
        $exist = false;

        if(!isset($this->checkedRow['office_phone'])) {
            return true;
        }

        foreach ($this->office->phones as $phone) {
            if (
                ($phone->phone == $this->checkedRow['office_phone'])&&
                ($phone->source == $this->source->getSourceString())
               )
            {
                $exist = true;
                //$phone->addPassed();
            }
            //$phone->addChecked();
            //$phone->save();
        }

        if (!$exist) {
            $this->addPhone();
        }
    }

    private function updateZip() {
        if(!isset($this->checkedRow['zip'])) {
            return true;
        }

        $exist = false;
        foreach ($this->office->zips as $zip) {
            if (
                ($zip->zip == $this->checkedRow['zip'])&&
                ($zip->source == $this->source->getSourceString())
            )
            {
                $exist = true;
                //$zip->addPassed();
            }
            //$zip->addChecked();
            //$zip->save();
        }

        if (!$exist) {
            $this->addZip();
        }
    }

    private function updateState() {

        $exist = false;

        if(!isset($this->checkedRow['state'])) {
            return true;
        }

        foreach ($this->office->states as $state) {
            if (
                ($state->state == $this->checkedRow['state'])&&
                ($state->source == $this->source->getSourceString())
            )
            {
                $exist = true;
                //$state->addPassed();
            }
            //$state->addChecked();
            //$state->save();
        }

        if (!$exist) {
            $this->addState();
        }
    }

    private function update() {
        $this->updateName();
    	$this->updateCompanyName();
    	$this->updateAddress();
    	$this->updatePhone();
    	$this->updateMsaId();
    	$this->updateMlsId();
    	$this->updateZip();
        $this->updateState();
    }

    private function addMsaId() {
    	if (isset($this->checkedRow['msa_id'])){
    		$relObject = new OfficeMsaId();
    		$relObject->msa_id = $this->checkedRow['msa_id'];
            $relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source->getSourceString();

    		$this->office->msaIds()->save($relObject);
    	}
    }

	private function addName() {
    	if (isset($this->checkedRow['office_name'])){
    		$relObject = new OfficeName();
    		$relObject->name = $this->checkedRow['office_name'];
    		$relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source->getSourceString();
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;

    		$this->office->names()->save($relObject);

    	}
    }

	private function addCompanyName() {

		if (isset($this->checkedRow['company_name'])){
    		$relObject = new OfficeCompanyName();
    		$relObject->company_name = $this->checkedRow['company_name'];
            $relObject->source_row_id = $this->sourceRowId;
    		$relObject->source = $this->source->getSourceString();
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;

    		$this->office->companyNames()->save($relObject);
    	}
    }

    private function addMlsId() {
        if (isset($this->checkedRow['mls_id'])) {
            $relObject = new OfficeMlsId();
            $relObject->mls_id = $this->checkedRow['mls_id'];
            $relObject->mls_name = $this->source->getMlsName();
            $relObject->source = $this->source->getSourceString();
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            $this->office->mlsIds()->save($relObject);
        }
    }

    private function addPhone() {

        if (isset($this->checkedRow['office_phone'])){
            $relObject = new OfficePhone();
            $relObject->phone = $this->checkedRow['office_phone'];
            $relObject->source = $this->source->getSourceString();
            $relObject->slug = StringHelpers::cleanupPhoneNumber($relObject->phone);
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            $this->office->phones()->save($relObject);

        }
    }

    private function addZip() {

        if (isset($this->checkedRow['zip'])){
            $relObject = new OfficeZip();
            $relObject->zip = $this->checkedRow['zip'];
            $relObject->source = $this->source->getSourceString();
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            $this->office->zips()->save($relObject);
        }
    }

    private function addState() {

        if (isset($this->checkedRow['state'])){
            $relObject = new OfficeState();
            $relObject->state = $this->checkedRow['state'];
            $relObject->source = $this->source->getSourceString();
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;
            $this->office->states()->save($relObject);
        }
    }

    private function addAddress() {
		if (isset($this->checkedRow['address1']) || isset($this->checkedRow['address2'])||isset($this->checkedRow['city']))
		{
    		$relObject = new OfficeAddress();
    		if (isset($this->checkedRow['address1'])) {
    			$relObject->address1 = $this->checkedRow['address1'];

       		}

    		if (isset($this->checkedRow['address2'])) {
    			$relObject->address2 = $this->checkedRow['address2'];
    		}

            if (isset($this->checkedRow['city'])) {
                $relObject->city = $this->checkedRow['city'];
            }

    		$relObject->source = $this->source->getSourceString();
            $relObject->source_row_id = $this->sourceRowId;
            $relObject->matching_rate = $this->matching_rate;
            $relObject->matched_by = $this->matched_by;

    		$this->office->addresses()->save($relObject);
    	}
	}

    private function create() {
    	$this->office = Office::create(['source' => $this->source->getSourceString()]);
    	$this->office->save();
        $this->matching_rate = 100;
        $this->matched_by = null;
    	$this->addMsaId();
    	$this->addName();
    	$this->addCompanyName();
    	$this->addAddress();
    	$this->addState();
        $this->addZip();
        $this->addPhone();
        $this->addMlsId();
        $this->addZip();
        $this->log('Adding the office: '.$this->office->id);

	   	return $this->office;
    }

    public function match() {

		if ($office = $this->search()){
            $this->log('Office found with id '.$office->id. ' by '.$this->matched_by);
            return $office;
        }

    	$office = $this->create();

    	return $office;

    }

    public function getId($row) {
        $this->checkedRow = $row;
        $this->sourceObjectId = $row['source_object']['source_object_id'];
        $this->office = $this->match();

        if (!($this->office->wasRecentlyCreated)) {
                $this->update();
        }

        return $this->office->id;
    }
}
