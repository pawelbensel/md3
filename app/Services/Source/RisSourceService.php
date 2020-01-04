<?php

namespace App\Services\Source;

class RisSourceService extends BaseDBSourceService
{
    
    public function __construct()
    {
        $this->setDBConnection('ris');
        $this->setTableName('marketing_email');
        $this->setMap();

    }
    
    public function setMap() {
        $this->mapArray['agent'] = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'title' => 'title',
            'type' => 'type',
        ];

        $this->mapArray['office'] = [
            'office_name' => 'office_name',
            'company_name' => 'company_name',
            'address1' => 'address1',
            'address2' => 'address2',
            'city' => 'city',
            'state' => 'state',
            'zip' => 'zip',
            'office_phone' => 'phone',
            'msa_id' => 'msa_id',

        ];
    }



    public function getData()
    {
        $this->data = \DB::connection($this->dbConnection)
                ->table($this->tableName)
                ->offset($this->offset)
                ->limit($this->limit)
                ->get();

        return $this->data;
    }

    public function parseData()
    {
        
        foreach ($this->data as $row) {
            $returnArray[] = $this->map($row);
        } 
        return $returnArray;
    }





   
}
