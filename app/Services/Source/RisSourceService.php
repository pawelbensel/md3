<?php

namespace App\Services\Source;

use Illuminate\Database\Eloquent\Collection;

class RisSourceService extends BaseDBSourceService
{

    public function __construct()
    {
        $this->setSource('ris_media');
        $this->setDBConnection('ris');
        $this->setTableName('marketing_email');
        $this->setMap();

    }
    /**
     * Map fields to destination structure from from source data
     *
     * @var string
     */
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
            'company_name' => 'company',
            'address1' => 'address1',
            'address2' => 'address2',
            'city' => 'city',
            'state' => 'state',
            'zip' => 'zip',
            'office_phone' => 'office_phone',
            'msa_id' => 'msa_id',

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
