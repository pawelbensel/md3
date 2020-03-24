<?php


namespace App\Services\Report\SQL;


use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Interfaces\MultiMlsableSql;
use App\Services\Report\Entity\ReportMap;

class ExampleSql extends ReportSql implements MultiMlsableSql, Mappable
{
    protected $sql = "
                      SELECT 
                        id,
                        mls_number,
                        ':orgMls' as mls_name,
                        'FULL MLS NAME' as mls_full_name,
                        street_name,
                        city,
                        state,
                        zip,
                        price,
                        status,
                        timestamp as status_date,
                        'John' as agent_name,
                        'Doe' as agent_last_name,
                        'dsa' as email,
                        '32432432' as phone,
                        '231312' as agent_mls_id,
                        ':orgMls' as agent_mls_name,
                        ':orgMls FULL ' as agent_mls_full,
                        'Office John' as office_name,
                        '312321312' as office_phone,
                        'DMNSAJD' as office_mls_id,
                        ':orgMls' as office_mls_name,
                        ':orgMls FULL' as office_mls_full,
                        '0101' as md3_mls_id,
                        '00' as md3_phones,
                        'bill' as dr_bill_agents,
                        'ris' as rismedia_agents,
                        'other' as other_agents,
                        'email' as md3_email
                      FROM :orgMls_prop 
                      WHERE id IN (1,2,3,4,5,6,7)
                     ";

    public function replaceMls(string $newOrgId)
    {
        $this->sql = preg_replace(':\:orgMls:', $newOrgId, $this->sql);
    }

    public function getMap(): ReportMap
    {
        return new ReportMap([
            0 => 'id',
            1 => 'mls_number',
            2 => 'mls_name',
            3 => 'mls_full_name',
            4 => 'street_name',
            5 => 'city',
            6 => 'state',
            7 => 'zip',
            8 => 'price',
            9 => 'status',
            10 => 'status_date',
            11 => 'agent_name',
            12 => 'agent_last_name',
            13 => 'email',
            14 => 'phone',
            15 => 'agent_mls_id',
            16 => 'agent_mls_name',
            17 => 'agent_mls_full',
            18 => 'office_name',
            19 => 'office_phone',
            20 => 'office_mls_id',
            21 => 'office_mls_name',
            22 => 'office_mls_full',
            23 => 'md3_mls_id',
            24 => 'md3_phones',
            25 => 'dr_bill_agents',
            26 => 'rismedia_agents',
            27 => 'other_agents',
            28 => 'md3_email',
        ]);
    }
}


