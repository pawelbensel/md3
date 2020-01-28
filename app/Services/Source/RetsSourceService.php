<?php

namespace App\Services\Source;


use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class RetsSourceService extends BaseDBSourceService implements MultiTableInterface
{
    const SUPPORTED_TABLES = ['agents', 'offices'];

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
        $this->mapArray['agent'] = [
            'first_name' =>'ag_first_name',
            'last_name' => 'ag_last_name',
            'email' => 'ag_email',
            'type' => 'ag_type',
            'phone' => 'ag_phone',
            'mls_id' => 'ag_public_id',
            'office_mls_id' => 'of_public_id',
        ];

        $this->mapArray['office'] = [
            'office_name' => 'of_name',
            'company' =>'company_name',
            'address1' => 'of_address',
            'city' => 'of_city',
            'state' => 'of_state',
            'zip' => 'of_zip',
            'office_phone' => 'of_phone',
            'mls_id' => 'of_public_id',
        ];
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
