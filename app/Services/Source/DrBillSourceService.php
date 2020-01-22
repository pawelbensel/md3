<?php

namespace App\Services\Source;

class DrBillSourceService extends BaseDBSourceService
{
    public function __construct()
    {
        $this->setSource('dr_bill');
        $this->setDBConnection('dr_bill');
        $this->setTableName('drbill');
        $this->setMap();

    }
    /**
     * Map fields to destination structure from from source data
     *
     * @var string
     */
    public function setMap() {
        $this->mapArray['agent'] = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'type' => 'License Type',
            'license_number' => 'License Number',
        ];

        $this->mapArray['office'] = [
            'office_name' => 'Office Name',
            'address1' => 'Office Address1',
            'address2' => 'Office Address2',
            'city' => 'Office City',
            'state' => 'Office State',
            'zip' => 'Office Zip',
            'office_phone' => 'Office Phone',
        ];
    }

    public function parseData()
    {

        foreach ($this->data as $row) {
            $returnArray[] = $this->map($row);
        }
        return $returnArray;
    }

}