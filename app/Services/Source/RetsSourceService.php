<?php

namespace App\Services\Source;


use App\Helpers\StringHelpers;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class RetsSourceService extends BaseDBSourceService implements MultiTableInterface
{
    const AGENTS_TABLE = 'agents';
    const OFFICES_TABLE = 'offices';
    const PROPERTIES_TABLE = 'prop';

    const SUPPORTED_TABLES = [
        self::AGENTS_TABLE,
        self::OFFICES_TABLE,
        self::PROPERTIES_TABLE
    ];

    public function __construct($table)
    {
        $this->setDBConnection('rets');
        $this->setTableName($this->resolveTableName($table));
        $this->setSource('rets_'.$this->tableName);
        $this->setMap();
    }

    public function getMlsName(): string
    {
        return strtok($this->tableName, '_');
    }

    public function setMap() {
        if(StringHelpers::contains($this->tableName, self::AGENTS_TABLE)){
            $this->mapArray['agent'] = [
                'first_name' =>'ag_first_name',
                'last_name' => 'ag_last_name',
                'email' => 'ag_email',
                'type' => 'ag_type',
                'phone' => 'ag_phone',
                'mls_id' => 'ag_public_id',
                'office_mls_id' => 'of_public_id',
            ];
        }
        if(StringHelpers::contains($this->tableName, self::OFFICES_TABLE)) {
            $this->mapArray['office'] = [
                'office_name' => 'of_name',
                'company' => 'company_name',
                'address1' => 'of_address',
                'city' => 'of_city',
                'state' => 'of_state',
                'zip' => 'of_zip',
                'office_phone' => 'of_phone',
                'mls_id' => 'of_public_id',
                'office_email' => 'of_email',
                'office_website' => 'of_website',
            ];
        }

        if(StringHelpers::contains($this->tableName, self::PROPERTIES_TABLE)) {
            $this->mapArray['prop'] = [
                'zip' => 'zip',
                'year_build' => 'year_built',
                'total_room' => 'total_rooms',
                'total_living_room' => 'total_livingroom',
                'total_family_room' => 'total_familyroom',
                'total_eat_in_kitchen' => 'total_eat_in_kitchen',
                'total_dining_room' => 'total_diningroom',
                'total_bed_room' => 'total_bedrooms',
                'basement' => 'basement',
                'garage' => 'garage',
                'square_feet' => 'square_feet',
                'description' => 'description',

                'street_unit' => 'street_unit',
                'street_suffix' => 'street_suffix',
                'street_post_direction' => 'street_post_direction',
                'street_number' => 'street_number',
                'street_name' => 'street_name',
                'street_direction' => 'street_direction',
                'county' => 'county',
                'city' => 'city',

                'status_date' => 'status_date',
                'status' => 'status',
                'soldprice' => 'soldprice',
                'price' => 'price',
                'picture_url' => 'picture_url',
                'on_market' => 'on_market',
                'mls_private_number' => 'mls_private_number',
                'mls_office_id' => 'mls_office_id',
                'mls_id' => 'mls_number',
                'mls_agent_id' => 'mls_agent_id',
                'mls_co_agent_id' => 'mls_co_agent_id',
            ];
        }
    }

    public function parseData()
    {

        foreach ($this->data as $row) {
            $returnArray[] = $this->map($row);
        }
        return $returnArray;
    }

    private function resolveTableName(string $table)
    {
        $found = false;

        if(!Schema::connection($this->dbConnection)->hasTable($table)){
            throw new \Exception('Table does not exists.');
        }

        foreach (self::SUPPORTED_TABLES as $supportedTable ) {
            $found = (!$found && (strpos($table, $supportedTable) !== false) ) ? true : false;
            if($found) {
                break;
            }
        }

        if(!$found) {
            throw new \Exception('Unsupported table');
        }

        return $table;
    }
}
