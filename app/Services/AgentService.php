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
use Illuminate\Database\Query\Builder;

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
        $agent = $this->matchByAll();

        if (null != $agent){
            $this->log('Agent found with id '.$agent->id);
            return $agent;
        }


        $agent = $this->create();

        return $agent;

    }

    private function matchByAll()
    {
        $firstName = StringHelpers::escapeLike($this->checkedRow['first_name']);
        $lastName = StringHelpers::escapeLike($this->checkedRow['last_name']);
        $title = (array_key_exists('title',$this->checkedRow))?
            StringHelpers::escapeLike($this->checkedRow['title'])
            :'';
        $type = StringHelpers::escapeLike($this->checkedRow['type']);
        $email = $this->checkedRow['email'];
        $mls_id = array_key_exists('mls_id',$this->checkedRow)? $this->checkedRow['mls_id']: null;
        $office_mls_id = array_key_exists('office_mls_id',$this->checkedRow)? $this->checkedRow['office_mls_id']: null;
        $license_number = array_key_exists('license_number',$this->checkedRow)? $this->checkedRow['license_number']: null;
        
        $this->log('Checking row: ');
        $this->log($this->checkedRow);

        $agent = null;

        
        if($license_number) {
            $this->matched_by = 'license_number';
            $this->log('Try to get by '. $this->matched_by);
            $this->matching_rate = 100;
            $agent = Agent::where('license_number', '=', $this->checkedRow['license_number'])->first();
        }


        $this->matched_by = 'first_name, last_name, email, title, type';
        $this->setBaseBuilder($this->matched_by);
        $this->matching_rate = 100;
        $this->log('Try to get by '. $this->matched_by);

        if(!$agent){
            $agent = $this->queryBuilder->select('agents.id')
                ->whereRaw('agent_first_names.first_name like \'%'.$firstName.'%\'')
                ->whereRaw('agent_last_names.last_name like \'%'.$lastName.'%\'')
                ->whereRaw('agent_emails.email like \'%'.$email.'%\'')
                ->whereRaw('agent_titles.title like \'%'.$title.'%\'')
                ->whereRaw('agent_types.type like \'%'.$type.'%\'')
                ->first();
        }

        if(!$agent && $email) {
            $this->matched_by = 'first_name, last_name, email';
            $this->setBaseBuilder($this->matched_by);
            $this->matching_rate = 100;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->whereRaw('agent_first_names.first_name like \'%'.$firstName.'%\'')
                ->whereRaw('agent_last_names.last_name like \'%'.$lastName.'%\'')
                ->whereRaw('agent_emails.email like \'%'.$email.'%\'')
                ->first();
        }

        if(!$agent && $email) {
            $this->matched_by = 'soundex first_name, soundex last_name, email';
            $this->setBaseBuilder('first_name, last_name, email');
            $this->matching_rate = 70;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->whereRaw("levenshtein_ratio('".\Str::slug($firstName,'')."', agent_first_names.slug) >80")
                ->whereRaw("levenshtein_ratio('".\Str::slug($lastName,'')."', agent_last_names.slug) >80")
                ->whereRaw('agent_emails.email like \'%'.$email.'%\'')
                ->first();
        }

        if(null == $agent && null != $this->officeIdScope) {
            $this->matched_by = 'first_name, last_name, office_id';
            $this->setBaseBuilder('first_name, last_name');
            $this->matching_rate = 90;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->leftJoin('agent_office on agent_office.agent_id=agent.id')
                ->whereRaw('agent_first_names.first_name like \'%'.$firstName.'%\'')
                ->whereRaw('agent_last_names.last_name like \'%'.$lastName.'%\'')
                ->whereRaw('agent_office.office_id = '.(int)$this->officeIdScope.'')
                ->first();
        }

        if(null == $agent && null != $mls_id) { //add compare mls_name
            $this->matched_by = 'first_name, last_name, mls_id, mls_name';
            $this->setBaseBuilder($this->matched_by);
            $this->matching_rate = 80;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->whereRaw('agent_first_names.first_name like \'%'.$firstName.'%\'')
                ->whereRaw('agent_last_names.last_name like \'%'.$lastName.'%\'')
                ->whereRaw("agent_mls_ids.mls_id like '%".$mls_id."%' and agent_mls_ids.mls_name='".$this->mlsName."'")
                ->first();
        }

        
        if(null == $agent && null != $office_mls_id) {
            $this->matched_by = 'first_name, last_name, office_id';
            $this->setBaseBuilder('first_name, last_name');
            $this->matching_rate = 90;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->leftJoin('agent_office','agent_office.agent_id','=','agent.id')
                ->leftJoin('office_mls_ids','office_mls_ids.office_id','=','agent_office.office_id')
                ->whereRaw('agent_first_names.first_name like \'%'.$firstName.'%\'')
                ->whereRaw('agent_last_names.last_name like \'%'.$lastName.'%\'')
                ->whereRaw("office_mls_ids.mls_id like '%".$mls_id."%' and agent_mls_ids.mls_name='".$this->mlsName."'")
                ->first();
        }

        if(null == $agent && null != $mls_id) {
            $this->matched_by = 'soundex first_name, soundex last_name, mls_id, mls_name';
            $this->setBaseBuilder('first_name, last_name, mls_id');
            $this->matching_rate = 70;
            $this->log('Try to get by '. $this->matched_by);
            $agent = $this->queryBuilder->select('agents.id')
                ->whereRaw("levenshtein_ratio('".\Str::slug($firstName,'')."', agent_first_names.slug) >80")
                ->whereRaw("levenshtein_ratio('".\Str::slug($lastName,'')."', agent_last_names.slug) >80")
                ->whereRaw("agent_mls_ids.mls_id like '%".$mls_id."%' and agent_mls_ids.mls_name='".$this->mlsName."'")
                ->first();
        }

        return $agent;
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

    private function setBaseBuilder($usedFields)
    {
        $this->queryBuilder = ($this->officeIdScope != null)?
            Agent::whereHas('offices', function($q){
              $q->where('offices.id', $this->officeIdScope);
            })
            : Agent::query();

        $matchedBy = explode(', ', $usedFields);
        array_walk($matchedBy, function(&$singleMatch) {
            $singleMatch = $singleMatch.'s';
        });

        foreach ($matchedBy as $singleMatch){
            $this->queryBuilder =
                $this->queryBuilder->leftJoin(
                    'agent_'.$singleMatch,
                    'agents.id',
                    '=',
                    'agent_'.$singleMatch.'.agent_id'
                );
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
