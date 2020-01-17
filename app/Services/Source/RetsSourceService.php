<?php

namespace App\Services\Source;


use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class RetsSourceService extends BaseDBSourceService
{
    private $tables = ['agents', 'offices'];
    private $org_id;


    public function __construct(string $orgId, string $table)
    {
        if(!in_array($table,$this->tables)){
            throw new InvalidParameterException('Invalid table parameter');
        }
        $this->org_id = $orgId;

        $this->setDBConnection('rets');
        $this->setTableName($this->resolveTableName($orgId, $table));
        $this->setSource('rets_'.$this->tableName);
        $this->setMap();
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

    public function getOrgId()
    {
        return $this->org_id;
    }

    public function parseData()
    {

        foreach ($this->data as $row) {
            $returnArray[] = $this->map($row);
        }
        return $returnArray;
    }

    private function resolveTableName(string $orgId, string $table)
    {
        $combinations = [
            '_',    'mls_',
            '_bo_', 'mls_bo_',
            '_ds_', 'mls_ds_',
            '_ftp_','mls_ftp_',
            '_idx_','mls_idx_'

        ];

        foreach($combinations as $combination) {
            $tableName = $orgId.$combination.$table;
            if( Schema::connection($this->dbConnection)->hasTable($tableName)){
                return $tableName;
            }
        }

        throw new \Exception('Coudnt find table for this org id');
    }
}