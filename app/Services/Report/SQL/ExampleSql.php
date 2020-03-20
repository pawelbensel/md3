<?php


namespace App\Services\Report\SQL;


use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Interfaces\MultiMlsableSql;
use App\Services\Report\Interfaces\ReportMap;

class ExampleSql extends ReportSql implements MultiMlsableSql, Mappable
{
    protected $sql = "
                      SELECT 
                        id,
                        mls_number,
                        mls_agent_id,
                        street_name
                      FROM :orgMls_prop 
                      WHERE id IN (1,2,3,4,5,6,7)
                     ";

    public function replaceMls(string $newOrgId)
    {
        $this->sql = str_replace(':orgMls', $newOrgId, $this->sql);
    }

    public function getMap(): ReportMap
    {
        return new ReportMap([
            0 => 'id',
            1 => 'mls_number',
            2 => 'mls_agent_id',
            3 => 'street_name',
        ]);
    }
}
