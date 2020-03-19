<?php


namespace App\Services\Report\Source;


use App\Services\Report\Interfaces\SqlInjectable;
use App\Services\Report\LookupStrategy\LookupStrategy;
use App\Services\Report\SQL\ReportSql;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class RetsReportSource extends DatabaseReportSource implements SqlInjectable
{
    /** @var ReportSql $sql */
    protected $sql;

    public function __construct()
    {
        $this->setConnection(DB::connection('rets'));
    }

    /**
     * @return mixed|void
     */
    public function getData()
    {
        $this->getConnection()->raw($this->sql);
    }

    /**
     * @param ReportSql $sql
     */
    public function setSql(ReportSql $sql)
    {
        $this->sql = $sql->getSql();
    }
}
