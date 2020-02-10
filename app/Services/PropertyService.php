<?php

namespace App\Services;


use App\Helpers\StringHelpers;
use App\Models\Agent;
use App\Models\AgentMlsId;
use App\Models\KeyValue;
use App\Models\OfficeMlsId;
use App\Models\Prop;
use App\Models\PropAddress;
use App\Models\PropAgentMlsId;
use App\Models\PropBasement;
use App\Models\PropDescription;
use App\Models\PropGarage;
use App\Models\PropMlsId;
use App\Models\PropMlsOfficeId;
use App\Models\PropMlsPrivateNumber;
use App\Models\PropOnMarket;
use App\Models\PropPictureUrl;
use App\Models\PropPrice;
use App\Models\PropSoldPrice;
use App\Models\PropSquareFeet;
use App\Models\PropStatus;
use App\Models\PropTotalBedRoom;
use App\Models\PropTotalDiningRoom;
use App\Models\PropTotalEatInKitchen;
use App\Models\PropTotalFamilyRoom;
use App\Models\PropTotalLivingRoom;
use App\Models\PropTotalRoom;
use App\Models\PropYearBuild;
use App\Models\PropZip;
use App\Models\Similar;
use App\Models\PropLDate;
use App\Services\Matcher\MatcherInterface;
use App\Services\Source\RetsSourceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class PropertyService extends BaseService implements ParseServiceInterface
{
    /** @var Prop */
    protected $property;
    protected $sourceObjectId;
    protected $sourceRowId;
    private $matching_rate;
    private $matched_by;

    public function getId($row)
    {
        $this->checkedRow = $row;
        $this->sourceObjectId = $row['source_object']['source_object_id'];
        $this->property = $this->match();
        if (!$this->property->wasRecentlyCreated) {
            $this->update();
        }

        return $this->property->id;
    }

    public function match()
    {
        $property = $this->search();

        if (null != $property && $this->matching_rate > 50){
            echo 'Property found with id '.$property->id. ' by '.$this->matched_by.PHP_EOL;
            return $property;
        }
        $LowMatchingRateProperty = $property;
        $previousMatchingRate = $this->matching_rate;
        $previousMatchedBy = $this->matched_by;

        $property = $this->create();

        if(null != $LowMatchingRateProperty && $previousMatchingRate <= 50){
            $similar = new Similar();
            $similar->object_id = $LowMatchingRateProperty->id;
            $similar->similar_id = $property->id;
            $similar->matched_by = $previousMatchedBy;
            $similar->matching_rate = $previousMatchingRate;
            echo 'Found low similarity object'.PHP_EOL;

            $similar->similar()->associate($this->property)->save();
        }

        return $property;
    }

    public function setSourceRowId($sourceRowId)
    {
        $this->sourceRowId = $sourceRowId;
    }

    private function search()
    {
        $property = null;
        $row = $this->getPreparedRow();
        $files = File::allFiles(app_path('Services/Matcher/Matchers'));
        foreach ($files as $file) {
            $class = '\\App\\Services\\Matcher\\Matchers\\' . $file->getBasename('.php');
            /** @var MatcherInterface $matcher */
            $matcher = new $class();
            if (!$matcher->supports($this)) {
                continue;
            }
            $property = $matcher->match($row);
            if ($property) {
                $this->matching_rate = $matcher->getRate();
                $this->matched_by = $matcher->getMatchedBy();
                break;
            }
        }

        return $property;
    }


    private function create()
    {
        $this->property = Prop::create(['source' => $this->source->getSourceString()]);
        $this->matching_rate = 100;
        $this->matched_by = null;
        $this->addZip();
        $this->addYearBuild();
        $this->addTotalRoom();
        $this->addTotalLivingRoom();
        $this->addTotalFamilyRoom();
        $this->addTotalDiningRoom();
        $this->addTotalEatinKitchen();
        $this->addTotalBedRoom();
        $this->addBasement();
        $this->addGarage();
        $this->addAddress();
        $this->addStatus();
        $this->addSquareFeet();
        $this->addPictureUrl();
        $this->addOnMarket();
        $this->addDescription();
        $this->addMlsId();
        $this->addMlsPrivateNumber();
        $this->addMlsOfficeId();
        $this->addPrimaryMlsAgent();
        $this->addCoMlsAgent();
        $this->addLDate();
        $this->addKeyValues();

        echo 'Adding the property: '.$this->property->id.PHP_EOL;

        foreach ($this->property->agentMlsIds as $agentMlsId) {

            $agentMlsId = AgentMlsId::where('mls_id', '=', $agentMlsId->agent_mls_id)->where('mls_name', '=', $this->source->getMlsName())->first();
            if(!$agentMlsId){
                continue;
            }
            $agent = $agentMlsId->agent()->get()->first();

            echo 'Assigning agent '.$agent->id.' to property.';
            $this->property->agents()->attach($agent);
        }

        foreach ($this->property->mlsOfficeIds as $officeId) {
            $officeMlsId = OfficeMlsId::where('mls_id', '=', $officeId->mls_office_id)->where('mls_name', '=', $this->source->getMlsName())->first();
            if(!$officeMlsId){
                continue;
            }
            $office = $officeMlsId->office()->get()->first();
            echo 'Assigning office '.$office->id.' to property.';
            $this->property->offices()->attach($office);
        }

        return $this->property;
    }

    private function update(){
        $this->updateZip();
        $this->updateYearBuild();
        $this->updateTotalRoom();
        $this->updateTotalLivingRoom();
        $this->updateTotalFamilyRoom();
        $this->updateTotalEaitInKitchen();
        $this->updateTotalDiningRoom();
        $this->updateTotalBedRoom();
        $this->updateBasement();
        $this->updateGarage();
        $this->updateAddress();
        $this->updateStatus();
        $this->updateSquareFeet();
        $this->updateDescription();
        $this->updatePictureUrl();
        $this->updateLDate();
        $this->updateOnMarket();
        $this->updateMlsId();
        $this->updateMlsPrivateNumber();
        $this->updateMlsOfficeId();
        $this->updateCoAgent();
        $this->updatePrimaryAgent();
        $this->updateKeyValues();
    }

    private function addZip() {
        if (isset($this->checkedRow['zip'])) {
            $relatedObject = new PropZip();
            $relatedObject->zip = $this->checkedRow['zip'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->zips()->save($relatedObject);
        }
    }

    private function addDescription() {
        if (isset($this->checkedRow['description'])) {
            $relatedObject = new PropDescription();
            $relatedObject->description = $this->checkedRow['description'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->descriptions()->save($relatedObject);
        }
    }

    private function addLDate() {
        if (isset($this->checkedRow['l_date'])) {
            $relatedObject = new PropLDate();
            $relatedObject->l_date = $this->checkedRow['l_date'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->lDates()->save($relatedObject);
        }
    }

    private function addYearBuild() {
        if (isset($this->checkedRow['year_build'])) {
            $relatedObject = new PropYearBuild();
            $relatedObject->year_build = $this->checkedRow['year_build'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->yearBuilds()->save($relatedObject);
        }
    }

    private function addTotalRoom() {
        if (isset($this->checkedRow['total_room'])) {
            $relatedObject = new PropTotalRoom();
            $relatedObject->total_room = $this->checkedRow['total_room'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalRooms()->save($relatedObject);
        }
    }

    private function addTotalLivingRoom() {
        if (isset($this->checkedRow['total_living_room'])) {
            $relatedObject = new PropTotalLivingRoom();
            $relatedObject->total_living_room = $this->checkedRow['total_living_room'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalLivingRooms()->save($relatedObject);
        }
    }

    private function addTotalFamilyRoom() {
        if (isset($this->checkedRow['total_family_room'])) {
            $relatedObject = new PropTotalFamilyRoom();
            $relatedObject->total_family_room = $this->checkedRow['total_family_room'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalFamilyRooms()->save($relatedObject);
        }
    }

    private function addTotalEatinKitchen() {
        if (isset($this->checkedRow['total_eat_in_kitchen'])) {
            $relatedObject = new PropTotalEatInKitchen();
            $relatedObject->total_eat_in_kitchen = $this->checkedRow['total_eat_in_kitchen'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalEatInKitchens()->save($relatedObject);
        }
    }

    private function addTotalDiningRoom() {
        if (isset($this->checkedRow['total_dining_room'])) {
            $relatedObject = new PropTotalDiningRoom();
            $relatedObject->total_dining_room = $this->checkedRow['total_dining_room'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalDiningRooms()->save($relatedObject);
        }
    }

    private function addTotalBedRoom() {
        if (isset($this->checkedRow['total_bed_room'])) {
            $relatedObject = new PropTotalBedRoom();
            $relatedObject->total_bed_room = $this->checkedRow['total_bed_room'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->totalBedRooms()->save($relatedObject);
        }
    }

    private function addBasement() {
        if (isset($this->checkedRow['basement'])) {
            $relatedObject = new PropBasement();
            $relatedObject->basement = $this->checkedRow['basement'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->basements()->save($relatedObject);
        }
    }

    private function addGarage() {
        if (isset($this->checkedRow['garage'])) {
            $relatedObject = new PropGarage();
            $relatedObject->garage = $this->checkedRow['garage'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->garages()->save($relatedObject);
        }
    }

    private function addAddress() {
        $relatedObject = new PropAddress();

        if (isset($this->checkedRow['street_unit'])) {
            $relatedObject->street_unit = $this->checkedRow['street_unit'];
        }
        if (isset($this->checkedRow['street_suffix'])) {
            $relatedObject->street_suffix = $this->checkedRow['street_suffix'];
        }
        if (isset($this->checkedRow['street_post_direction'])) {
            $relatedObject->street_post_direction = $this->checkedRow['street_post_direction'];
        }
        if (isset($this->checkedRow['street_number'])) {
            $relatedObject->street_number = $this->checkedRow['street_number'];
        }

        if (isset($this->checkedRow['street_name'])) {
            $relatedObject->street_name = $this->checkedRow['street_name'];
        }

        if (isset($this->checkedRow['street_direction'])) {
            $relatedObject->street_direction = $this->checkedRow['street_direction'];
        }

        if (isset($this->checkedRow['county'])) {
            $relatedObject->county = $this->checkedRow['county'];
        }

        if (isset($this->checkedRow['city'])) {
            $relatedObject->city = $this->checkedRow['city'];
        }

            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->addresses()->save($relatedObject);

    }

    private function addStatus(): ?PropStatus {
        $status = null;
        if(isset($this->checkedRow['status']) && !$status){
            $status = new PropStatus();
            $status->status = $this->checkedRow['status'];
            if (isset($this->checkedRow['status_date'])) {
                $status->status_date =  $this->checkedRow['status_date'];
                $status->status_date_type = 'status_date';
            } else if(isset($this->checkedRow['updtime'])){
                $status->status_date =  $this->checkedRow['updtime'];
                $status->status_date_type = 'updtime';
            } else {
                $status->status_date = date('Y-m-d H:i:s');
                $status->status_date_type = 'now';
            }
            $status->source = $this->source->getSourceString();
            $status->source_row_id = $this->sourceRowId;
            $status->matching_rate = $this->matching_rate;
            $status->matched_by = $this->matched_by;
            $this->property->statuses()->save($status);
        }

        return $status;
    }

    private function addPrizeForStatus(PropStatus $status)
    {
        if(isset($this->checkedRow['price'])){
            $prize = $this->createPrize();
            $status->prices()->save($prize);
        }
    }

    private function addSoldPrizeForStatus(PropStatus $status)
    {
        if(isset($this->checkedRow['soldprice'])){
            $prize = $this->createSoldPrice();
            $status->prices()->save($prize);
        }
    }

    private function addKeyValues() {
        $keyValuesToInsert = [];
        $now = date('Y-m-d H:i:s');
        if (isset($this->checkedRow['keyvalue']) &&
            count($this->checkedRow['keyvalue']) > 0 ) {
            foreach ($this->checkedRow['keyvalue'] as $key => $value) {
                array_push($keyValuesToInsert,
                    [
                        'key' => $key,
                        'value' => $value,
                        'source' => $this->source->getSourceString(),
                        'owner_id' => $this->property->id,
                        'owner_type' => get_class($this->property),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                //$keyVal->owner()->associate($this->property)->save();
            }

            KeyValue::insert($keyValuesToInsert);
        }
    }

    private function addKeyValue(string $key, string $value) {
        if (isset($key) && isset($value)) {
            $keyVal = new KeyValue();
            $keyVal->key = $key;
            $keyVal->value = $value;
            $keyVal->source = $this->source->getSourceString();

            $keyVal->owner()->associate($this->property)->save();
        }
    }

    private function createSoldPrice(): ?Model {
        $relatedObject = null;
        if (isset($this->checkedRow['soldprice'])) {
            $relatedObject = new PropPrice();
            $relatedObject->price = $this->checkedRow['soldprice'];
            $relatedObject->price_type = 'S';
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
        }

        return $relatedObject;
    }

    private function addSquareFeet() {
        if (isset($this->checkedRow['square_feet'])) {
            $relatedObject = new PropSquareFeet();
            $relatedObject->square_feet = $this->checkedRow['square_feet'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->squareFeets()->save($relatedObject);
        }
    }

    private function createPrize(): Model {
        $relatedObject = null;
        if (isset($this->checkedRow['price'])) {
            $relatedObject = new PropPrice();
            $relatedObject->price = $this->checkedRow['price'];
            $relatedObject->price_type = 'P';
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
        }

        return $relatedObject;
    }

    private function addPictureUrl() {
        if (isset($this->checkedRow['picture_url'])) {
            $relatedObject = new PropPictureUrl();
            $relatedObject->picture_url = $this->checkedRow['picture_url'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->pictureUrls()->save($relatedObject);
        }
    }

    private function addOnMarket() {
        if (isset($this->checkedRow['on_market'])) {
            $relatedObject = new PropOnMarket();
            $relatedObject->on_market = $this->checkedRow['on_market'];
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->onMarkets()->save($relatedObject);
        }
    }

    private function addMlsPrivateNumber() {
        if (isset($this->checkedRow['mls_private_number'])) {
            $relatedObject = new PropMlsPrivateNumber();
            $relatedObject->mls_private_number = $this->checkedRow['mls_private_number'];
            $relatedObject->mls_name = $this->source->getMlsName();
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->mlsPrivateNumbers()->save($relatedObject);
        }
    }

    private function addMlsOfficeId() {
        if (isset($this->checkedRow['mls_office_id'])) {
            $relatedObject = new PropMlsOfficeId();
            $relatedObject->mls_office_id = $this->checkedRow['mls_office_id'];
            $relatedObject->mls_name = $this->source->getMlsName();
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->mlsOfficeIds()->save($relatedObject);
        }
    }

    private function addMlsId() {
        if (isset($this->checkedRow['mls_id'])) {
            $relatedObject = new PropMlsId();
            $relatedObject->mls_id = $this->checkedRow['mls_id'];
            $relatedObject->mls_name = $this->source->getMlsName();
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->mlsIds()->save($relatedObject);
        }
    }

    private function addPrimaryMlsAgent() {
        if (isset($this->checkedRow['mls_agent_id'])) {
            $relatedObject = new PropAgentMlsId();
            $relatedObject->agent_mls_id = $this->checkedRow['mls_agent_id'];
            $relatedObject->type = 'primary';
            $relatedObject->mls_name = $this->source->getMlsName();
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->agentMlsIds()->save($relatedObject);
        }
    }

    private function addCoMlsAgent()
    {
        if (isset($this->checkedRow['mls_co_agent_id'])) {
            $relatedObject = new PropAgentMlsId();
            $relatedObject->agent_mls_id = $this->checkedRow['mls_co_agent_id'];
            $relatedObject->type = 'co';
            $relatedObject->mls_name = $this->source->getMlsName();
            $relatedObject->source = $this->source->getSourceString();
            $relatedObject->source_row_id = $this->sourceRowId;
            $relatedObject->matching_rate = $this->matching_rate;
            $relatedObject->matched_by = $this->matched_by;
            $this->property->agentMlsIds()->save($relatedObject);
        }
    }

    private function updateZip()
    {
        $exist = false;
        foreach ($this->property->zips as $zip) {
            if (
                ($zip->zip == $this->checkedRow['zip'])&&
                ($zip->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addZip();
        }
    }

    private function updateDescription()
    {
        $exist = false;
        foreach ($this->property->descriptions as $description) {
            if (
                ($description->description == $this->checkedRow['description'])&&
                ($description->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addDescription();
        }
    }

    private function updateYearBuild()
    {
        $exist = false;
        foreach ($this->property->yearBuilds as $year_build) {
            if (
                ($year_build->year_build == $this->checkedRow['year_build'])&&
                ($year_build->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addYearBuild();
        }
    }

    private function updateLDate()
    {
        $exist = false;
        foreach ($this->property->lDates as $lDate) {
            if (
                ($lDate->l_date== $this->checkedRow['l_date'])&&
                ($lDate->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addLDate();
        }
    }

    private function updateTotalRoom()
    {
        $exist = false;
        foreach ($this->property->totalRooms as $totalRoom) {
            if (
                ($totalRoom->total_room == $this->checkedRow['total_room'])&&
                ($totalRoom->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalRoom();
        }
    }

    private function updateTotalLivingRoom()
    {
        $exist = false;
        foreach ($this->property->totalLivingRooms as $totalLivingRoom) {
            if (
                ($totalLivingRoom->total_living_room == $this->checkedRow['total_living_room'])&&
                ($totalLivingRoom->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalLivingRoom();
        }
    }

    private function updateTotalFamilyRoom()
    {
        $exist = false;
        foreach ($this->property->totalFamilyRooms as $totalFamilyRoom) {
            if (
                ($totalFamilyRoom->total_family_room == $this->checkedRow['total_family_room'])&&
                ($totalFamilyRoom->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalFamilyRoom();
        }
    }

    private function updateTotalEaitInKitchen()
    {
        $exist = false;
        foreach ($this->property->totalEatInKitchens as $totalEatInKitchen) {
            if (
                ($totalEatInKitchen->total_eat_in_kitchen == $this->checkedRow['total_eat_in_kitchen'])&&
                ($totalEatInKitchen->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalEatinKitchen();
        }
    }

    private function updateTotalDiningRoom()
    {
        $exist = false;
        foreach ($this->property->totalDiningRooms as $totalDiningRoom) {
            if (
                ($totalDiningRoom->total_dining_room == $this->checkedRow['total_dining_room'])&&
                ($totalDiningRoom->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalDiningRoom();
        }
    }

    private function updateTotalBedRoom()
    {
        $exist = false;
        foreach ($this->property->totalBedRooms as $totalBedRoom) {
            if (
                ($totalBedRoom->total_bed_room == $this->checkedRow['total_bed_room'])&&
                ($totalBedRoom->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addTotalBedRoom();
        }
    }

    private function updateBasement()
    {
        $exist = false;
        foreach ($this->property->basements as $basement) {
            if (
                ($basement->basement == $this->checkedRow['basement'])&&
                ($basement->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addBasement();
        }
    }

    private function updateGarage()
    {
        $exist = false;
        foreach ($this->property->garages as $garage) {
            if (
                ($garage->garage == $this->checkedRow['garage'])&&
                ($garage->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addGarage();
        }
    }

    private function updateAddress()
    {
        $exist = false;
        foreach ($this->property->addresses as $address) {
            if (
                ($address->street_unit == $this->checkedRow['street_unit'])&&
                ($address->street_sufix == $this->checkedRow['street_suffix'])&&
                ($address->street_post_direction == $this->checkedRow['street_post_direction'])&&
                ($address->street_number == $this->checkedRow['street_number'])&&
                ($address->street_name == $this->checkedRow['street_name'])&&
                ($address->street_direction == $this->checkedRow['street_direction'])&&
                ($address->street_county == $this->checkedRow['county'])&&
                ($address->street_city == $this->checkedRow['city'])&&
                ($address->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addAddress();
        }
    }

    private function updateStatus()
    {
        $statusDate = (isset($this->checkedRow['status_date']))? $this->checkedRow['status_date']: null;
        $statusDate = ($statusDate == null && isset($this->checkedRow['updtime']))? $this->checkedRow['updtime']: $statusDate;
        $statusExist = $priceExist = $soldPriceExist = false;
        $foundStatus = null;
        foreach ($this->property->statuses as $status) {
            if(!$statusDate){
                if (
                    ($status->status == $this->checkedRow['status'])&&
                    ($status->source == $this->source->getSourceString())
                )
                {
                    $foundStatus = $status;
                }
                foreach ($status->prices() as $price){
                    if (
                        ($status->status == $this->checkedRow['status'])&&
                        ($status->source == $this->source->getSourceString())&&
                        ($price->price == $this->checkedRow['price'])&&
                        ($price->price_type == 'P')
                    )
                    {
                        $priceExist = true;
                    }
                    if (
                        ($status->status == $this->checkedRow['status'])&&
                        ($status->source == $this->source->getSourceString())&&
                        ($price->price == $this->checkedRow['soldprice'])&&
                        ($price->price_type == 'S')
                    )
                    {
                        $soldPriceExist = true;
                    }
                }
            }else {
                if (
                    ($status->status == $this->checkedRow['status'])&&
                    ($status->status_date == $statusDate)&&
                    ($status->source == $this->source->getSourceString())
                )
                {
                    $foundStatus = $status;
                }
                foreach ($status->prices() as $price){
                    if (
                        ($status->status == $this->checkedRow['status'])&&
                        ($status->status_date == $statusDate)&&
                        ($status->source == $this->source->getSourceString())&&
                        ($price->price == $this->checkedRow['price'])&&
                        ($price->price_type == 'P')
                    )
                    {
                        $priceExist = true;
                    }
                    if (
                        ($status->status == $this->checkedRow['status'])&&
                        ($status->status_date == $statusDate)&&
                        ($status->source == $this->source->getSourceString())&&
                        ($price->price == $this->checkedRow['soldprice'])&&
                        ($price->price_type == 'S')
                    )
                    {
                        $soldPriceExist = true;
                    }
                }
            }
        }

        if(!$foundStatus){
            $foundStatus = $this->addStatus();
        }


        if(!$priceExist){
            $this->addPrizeForStatus($foundStatus);
        }

        if(!$soldPriceExist){
            $this->addSoldPrizeForStatus($foundStatus);
        }
    }

    private function updateSquareFeet()
    {
        $exist = false;
        foreach ($this->property->squareFeets as $squareFeet) {
            if (
                ($squareFeet->square_feet == $this->checkedRow['square_feet'])&&
                ($squareFeet->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addSquareFeet();
        }
    }

    private function updatePictureUrl()
    {
        $exist = false;
        foreach ($this->property->pictureUrls as $pictureUrl) {
            if (
                ($pictureUrl->picture_url == $this->checkedRow['picture_url'])&&
                ($pictureUrl->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addPictureUrl();
        }
    }

    private function updateOnMarket()
    {
        $exist = false;
        foreach ($this->property->onMarkets as $onMarket) {
            if (
                ($onMarket->on_market == $this->checkedRow['on_market'])&&
                ($onMarket->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addOnMarket();
        }
    }

    private function updateKeyValues()
    {
        /** @var Collection $propertyKeyValues */
        $propertyKeyValues = $this->property->keyValues;
        if (isset($this->checkedRow['keyvalue']) &&
            count($this->checkedRow['keyvalue']) > 0 ) {
            $now = date('Y-m-d H:i:s');
            $keyValuesToInsert = [];
            foreach($this->checkedRow['keyvalue'] as $key => $value) {
                $exist = $propertyKeyValues
                    ->where('key', $key)
                    ->where('value', $value)
                    ->where('source', $this->source->getSourceString())
                    ->first();
                if(!$exist){
                    array_push($keyValuesToInsert,
                        [
                            'key' => $key,
                            'value' => $value,
                            'source' => $this->source->getSourceString(),
                            'owner_id' => $this->property->id,
                            'owner_type' => get_class($this->property),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                }
            }
            KeyValue::insert($keyValuesToInsert);
        }
    }

    private function updateMlsPrivateNumber()
    {
        $exist = false;
        foreach ($this->property->mlsPrivateNumbers as $mlsPrivateNumber) {
            if (
                ($mlsPrivateNumber->mls_private_number == $this->checkedRow['mls_private_number'])&&
                ($mlsPrivateNumber->mls_name == $this->source->getMlsName())&&
                ($mlsPrivateNumber->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addMlsPrivateNumber();
        }
    }

    private function updateMlsOfficeId()
    {
        $exist = false;
        foreach ($this->property->mlsOfficeIds as $mlsOfficeId) {
            if (
                ($mlsOfficeId->mls_office_id == $this->checkedRow['mls_office_id'])&&
                ($mlsOfficeId->mls_name == $this->source->getMlsName())&&
                ($mlsOfficeId->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addMlsOfficeId();
        }
    }

    private function updateMlsId()
    {
        $exist = false;
        foreach ($this->property->mlsIds as $mlsId) {
            if (
                ($mlsId->mls_id == $this->checkedRow['mls_id'])&&
                ($mlsId->mls_name == $this->source->getMlsName())&&
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

    private function updatePrimaryAgent()
    {
        $exist = false;
        foreach ($this->property->agentMlsIds as $agentMlsId) {
            if (
                ($agentMlsId->agent_mls_id == $this->checkedRow['mls_agent_id'])&&
                ($agentMlsId->type == 'primary')&&
                ($agentMlsId->mls_name == $this->source->getMlsName())&&
                ($agentMlsId->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addPrimaryMlsAgent();
        }
    }

    private function updateCoAgent()
    {
        $exist = false;
        foreach ($this->property->agentMlsIds as $agentMlsId) {
            if (
                ($agentMlsId->agent_mls_id == $this->checkedRow['mls_agent_id'])&&
                ($agentMlsId->type == 'co')&&
                ($agentMlsId->mls_name == $this->source->getMlsName())&&
                ($agentMlsId->source == $this->source->getSourceString())
            )
            {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->addCoMlsAgent();
        }
    }

    /*private function addAttribute(string $attributeName, array $params = [])
    {

        if (isset($this->checkedRow[$attributeName])) {
            $relation = $this->resolveRelation($attributeName);
            $preopertyRelations = array_diff(get_class_methods(Prop::class), get_class_methods(Model::class));

            if(!in_array($relation,$preopertyRelations)){
                return null;
            }

            $modelClass = $this->resolveModel($attributeName);
            if(!$modelClass) {
              throw new \Exception('Couldnt find a model for attribute '.$attributeName);
            }

            $modelClass->$attributeName = $this->checkedRow[$attributeName];
            $modelClass->source = $this->source->getSourceString();
            //TODO: Below line slows down the process, Use model attributes instead.
            if(Schema::connection('mysql')->hasColumn($modelClass->getTable(), 'mls_name')){
                $modelClass->mls_name = $this->source->getMlsName();
            }

            $modelClass->source_row_id = $this->sourceRowId;
            $modelClass->matching_rate = $this->matching_rate;
            $modelClass->matched_by = $this->matched_by;
            $this->property->$relation()->save($modelClass);
        }
    }*/

    /*private function AddAddress($checkedRow)
    {
       $addressFields = ['street_unit', 'street_suffix', 'street_post_direction', 'street_number', 'street_name', 'street_direction', 'county', 'city',];
       $x = array_inter)

    }*/

    /*private function resolveModel(string $attributeName): ?Model
    {
        $toRemove = ['_','-'];
        $className = 'App\Models\Prop'.str_replace($toRemove, '',ucwords($attributeName, "\t\r\n\f\v\_\-" ));

        return new $className();
    }*/

    /*private function resolveRelation(string $attributeName): string
    {

        $exploded_str = explode('_', $attributeName);
        $exploded_str_camel = array_map('ucwords', $exploded_str);
        $relation = lcfirst(implode('', $exploded_str_camel));
        if (StringHelpers::endsWith($relation,'s')){
            $relation = $relation.'es';
        } else {
            $relation = $relation.'s';
        }
        return $relation;
    }*/

    private function getPreparedRow(): array
    {
        $sqlArray['zip'] = array_key_exists('zip',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['zip']): null;
        $sqlArray['year_build'] = array_key_exists('year_build',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['year_build']): null;
        $sqlArray['total_room'] = array_key_exists('total_room',$this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_room']) : null;
        $sqlArray['total_living_room'] = array_key_exists('total_living_room', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_living_room']): null;
        $sqlArray['total_family_room'] = array_key_exists('total_family_room', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_family_room']): null;
        $sqlArray['total_eat_in_kitchen'] = array_key_exists('total_eat_in_kitchen', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_eat_in_kitchen']): null;
        $sqlArray['total_diningroom'] = array_key_exists('total_diningroom', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_diningroom']): null;
        $sqlArray['total_bed_room'] = array_key_exists('total_bed_room', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['total_bed_room']): null;
        $sqlArray['basement'] = array_key_exists('basement', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['basement']): null;
        $sqlArray['square_feet'] = array_key_exists('square_feet', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['square_feet']): null;
        $sqlArray['description'] = array_key_exists('description', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['description']): null;

        $sqlArray['street_unit'] = array_key_exists('street_unit', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_unit']): null;
        $sqlArray['street_suffix'] = array_key_exists('street_suffix', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_suffix']): null;
        $sqlArray['street_post_direction'] = array_key_exists('street_post_direction', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_post_direction']): null;
        $sqlArray['street_number'] = array_key_exists('street_number', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_number']): null;
        $sqlArray['street_name'] = array_key_exists('street_name', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_name']): null;
        $sqlArray['street_direction'] = array_key_exists('street_direction', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['street_direction']): null;
        $sqlArray['county'] = array_key_exists('county', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['county']): null;
        $sqlArray['city'] = array_key_exists('city', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['city']): null;

        $sqlArray['status_date'] = array_key_exists('status_date', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['status_date']): null;
        $sqlArray['status'] = array_key_exists('status', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['status']): null;
        $sqlArray['soldprice'] = array_key_exists('soldprice', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['soldprice']): null;
        $sqlArray['price'] = array_key_exists('price', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['price']): null;
        $sqlArray['picture_url'] = array_key_exists('picture_url', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['picture_url']): null;
        $sqlArray['on_market'] = array_key_exists('on_market', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['on_market']): null;
        $sqlArray['mls_private_number'] = array_key_exists('mls_private_number', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['mls_private_number']): null;
        $sqlArray['mls_office_id'] = array_key_exists('mls_office_id', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['mls_office_id']): null;
        $sqlArray['mls_id'] = array_key_exists('mls_id', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['mls_id']): null;
        $sqlArray['mls_agent_id'] = array_key_exists('mls_agent_id', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['mls_agent_id']): null;
        $sqlArray['mls_co_agent_id'] = array_key_exists('mls_co_agent_id', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['mls_co_agent_id']): null;
        $sqlArray['l_date'] =  array_key_exists('l_date', $this->checkedRow)? StringHelpers::escapeLike($this->checkedRow['l_date']): null;

        $sqlArray['updtime'] = $this->checkedRow['updtime'];

        if($this->source instanceof RetsSourceService){
            $sqlArray['mls_name'] = $this->source->getMlsName();
        }

        return $sqlArray;
    }



}
