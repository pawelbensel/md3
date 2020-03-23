<?php


namespace App\Services\Report\Source;


use App\Console\Commands\CommandArguments;
use App\Services\Report\Interfaces\MultiMlsableSource;
use App\Services\Report\Interfaces\MultiMlsableSql;
use App\Services\Report\Interfaces\SqlInjectable;
use App\Services\Report\Entity\Data;
use App\Services\Report\SQL\ReportSql;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class RetsReportSource extends DatabaseReportSource implements SqlInjectable, MultiMlsableSource
{
    /** @var ReportSql $sql */
    protected $sql;

    /** @var array $mlses */
    private $mlses;

    public function __construct()
    {
        $this->setConnection(DB::connection('rets'));
    }

    /**
     * @return mixed|void
     */
    public function getData()
    {
        $data = [];
        if($this->sql instanceof MultiMlsableSql) {
            foreach($this->mlses as $mls) {
                $this->sql->replaceMls($mls);
                $data = array_merge($data, (array)$this->getConnection()->select(DB::raw($this->sql->getSql())));
            }
        } else {
            $data = $this->getConnection()->statement($this->sql->getSql());
        }

        return $data;
    }

    /**
     * @param ReportSql $sql
     */
    public function setSql(ReportSql $sql)
    {
        $this->sql = $sql;
    }

    /**
     * @param array $mlsOrgIds
     */
    public function setMlses(array $mlsOrgIds)
    {
        $this->mlses = $mlsOrgIds;
    }

    /**
     * @return ReportSql|null
     */
    public function getSql(): ?ReportSql
    {
        return $this->sql;
    }
}
