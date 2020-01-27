<?php


namespace App\Services;


use App\Helpers\StringHelpers;
use App\Models\Agent;
use App\Models\AgentEmail;
use App\Models\AgentFirstName;
use App\Models\AgentLastName;
use App\Models\AgentMlsId;
use App\Models\AgentPhone;
use App\Models\AgentTitle;
use App\Models\AgentType;
use App\Models\Office;
use App\Models\OfficeMlsId;
use App\Services\Matcher\MatcherInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;


class AgentService extends BaseService implements ParseServiceInterface
{
    /** @var Agent */
    protected $agent;
    protected $sourceObjectId;
    protected $sourceRowId;
    protected $office;
    private  $officeIdScope;
    private $queryBuilder;
    private $mlsName;

    public function __construct($officeIdScope = null)
    {
        $this->officeIdScope = $officeIdScope;
    }

    public function setSourceRowId($sourceRowId) {
        $this->sourceRowId = $sourceRowId;
    }

    public function setOffice(Office $office) {
        $this->office = $office;
    }

    public function setMlsName(string $mlsName)
    {
        $this->mlsName = $mlsName;
    }

    public function getId($row) {

        $this->checkedRow = $row;

        $this->sourceObjectId = $row['source_object']['source_object_id'];
        $this->agent = $this->match();
        if (!$this->agent->wasRecentlyCreated) {
            $this->update();
        }

        return $this->agent->id;
    }

    public function match() {
        $agent = $this->search();

        if (null != $agent){
            $this->log('Agent found with id '.$agent->id);
            return $agent;
        }

        $agent = $this->create();

        return $agent;
    }

    private function search()
    {
        $this->log('Checking row: ');
        $this->log($this->checkedRow);

        $agent = null;
        $row = $this->getPreparedRow();

        $files = File::allFiles( app_path('Services/Matcher/Matchers'));
        foreach($files as $file) {
            $class = '\\App\\Services\\Matcher\\Matchers\\'.$file->getBasename('.php');
            /** @var MatcherInterface $matcher */
            $matcher = new $class();
            if (!$matcher->supports($this)){
                continue;
            }

            $agent = $matcher->match($row);

            if($agent) {
                $this->matching_rate = $matcher->getRate();
                $this->matched_by = $matcher->getMatchedBy();
                break;
            }
        }

        return $agent;
    }

    private function getPreparedRow(): array
    {
        $sqlArray['first_name'] = array_key_exists('first_name',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['first_name']): null;
        $sqlArray['last_name'] = array_key_exists('last_name',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['last_name']): null;
        $sqlArray['title'] = array_key_exists('title',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['title']) : null;
        $sqlArray['type'] = array_key_exists('email', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['type']): null;
        $sqlArray['email'] = array_key_exists('email', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['email']): null;
        $sqlArray['mls_id'] = array_key_exists('mls_id',$this->checkedRow)? $this->checkedRow['mls_id']: null;
        $sqlArray['office_mls_id'] = array_key_exists('office_mls_id',$this->checkedRow)? $this->checkedRow['office_mls_id']: null;
        $sqlArray['title'] = array_key_exists('license_number',$this->checkedRow)? $this->checkedRow['license_number']: null;

        return $sqlArray;
    }

    private function addFirstName() {
        if (isset($this->checkedRow['first_name'])) {
            $relatedObject = new AgentFirstName();
            $relatedObject->first_name = $this->checkedRow['first_name'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->firstNames()->save($relatedObject);
        }
    }

    private function addLastName() {
        if (isset($this->checkedRow['last_name'])) {
            $relatedObject = new AgentLastName();
            $relatedObject->last_name = $this->checkedRow['last_name'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->lastNames()->save($relatedObject);
        }
    }

    private function addEmail() {
        if (isset($this->checkedRow['email'])) {
            $relatedObject = new AgentEmail();
            $relatedObject->email = $this->checkedRow['email'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->emails()->save($relatedObject);
        }
    }

    private function addType() {
        if (isset($this->checkedRow['type'])) {
            $relatedObject = new AgentType();
            $relatedObject->type = $this->checkedRow['type'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->types()->save($relatedObject);
        }
    }

    private function addPhone() {
        if (isset($this->checkedRow['phone'])) {
            $relatedObject = new AgentPhone();
            $relatedObject->phone = $this->checkedRow['phone'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->phones()->save($relatedObject);
        }
    }

    private function addMlsId() {
        if (isset($this->checkedRow['mls_id'])) {
            $relatedObject = new AgentMlsId();
            $relatedObject->mls_id = $this->checkedRow['mls_id'];
            $relatedObject->mls_name = $this->mlsName;
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->agent->mlsIds()->save($relatedObject);
        }
    }

    private function log($string){
        if (is_string($string)) {
            echo $string."\n\r";
        } else {
            dump($string);
        }

    }

    private function update() {
        $this->updateFirstName();
        $this->updateLastName();
        $this->updateType();
        $this->updatePhone();
        $this->updateEmail();
        $this->updateMlsId();
    }

    private function updateFirstName()
    {
        $exist = false;
        foreach ($this->agent->firstNames as $firstName) {
            if (
                ($firstName->first_name == $this->checkedRow['first_name'])&&
                ($firstName->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addFirstName();
        }
    }

    private function updateLastName()
    {
        $exist = false;
        foreach ($this->agent->lastNames as $lastName) {
            if (
                ($lastName->last_name == $this->checkedRow['last_name'])&&
                ($lastName->last_name == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addLastName();
        }
    }

    private function updateType()
    {
        $exist = false;
        foreach ($this->agent->types as $type) {
            if (
                ($type->type == $this->checkedRow['type']) &&
                ($type->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addType();
        }
    }

    private function updatePhone()
    {
        $exist = false;
        foreach ($this->agent->phones as $phone) {
            if (
                ($phone->phone == $this->checkedRow['phone'])&&
                ($phone->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addPhone();
        }
    }

    private function updateEmail()
    {
        $exist = false;
        foreach ($this->agent->emails as $email) {
            if (
                ($email->email == $this->checkedRow['email'])&&
                ($email->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addEmail();
        }
    }

    private function updateMlsId()
    {
        $exist = false;
        foreach ($this->agent->mlsIds as $mlsId) {
            if (
                ($mlsId->mls_id == $this->checkedRow['mls_id'])&&
                ($mlsId->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addMlsId();
        }
    }

    private function create() {
        $this->agent = Agent::create(['source' => $this->source->getSourceString()]);
        $this->matching_rate = 100;
        $this->matched_by = null;
        $this->addFirstName();
        $this->addLastName();
        $this->addEmail();
        $this->addType();
        $this->addMlsId();
        $this->addPhone();
        $this->log('Adding the agent: '.$this->agent->firstNames()->first());
        $this->log($this->agent->id);
        // Set office for scoped searching
        if($this->officeIdScope) {
            $this->office = Office::find($this->officeIdScope);
        }
        // Set office if it is provided from source. When office is given with same row. Like RISmedia
        if($this->office){
            $this->office->agents()->attach($this->agent);
        }
        // Set office from office public id. Option for RETS.
        if (array_key_exists('office_mls_id', $this->checkedRow)) {
            $mlsId  = OfficeMlsId::where('mls_id','=',$this->checkedRow['office_mls_id'])->where('mls_name','=',$this->mlsName)->first();
            if($mlsId){
                $this->log('Office found with mls_id: '.$mlsId->mls_id);
                $office =  $mlsId->office()->get()->first();
            }else {
                $office = null;
            }
            if($office) {
                $this->office = $office;
                $this->office->agents()->attach($this->agent);
            }
        }

        return $this->agent;
    }

}
