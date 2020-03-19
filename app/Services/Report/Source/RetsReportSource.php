<?php


namespace App\Services\Report\Source;


use App\Console\Commands\CommandArguments;
use App\Services\Report\Interfaces\MultiMlsableSource;
use App\Services\Report\Interfaces\MultiMlsableSql;
use App\Services\Report\Interfaces\SqlInjectable;
use App\Services\Report\LookupStrategy\LookupStrategy;
use App\Services\Report\SQL\ReportSql;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class RetsReportSource extends DatabaseReportSource implements SqlInjectable, MultiMlsableSource
{
    /** @var CommandArguments $commandArguments */
    private $commandArguments;

    /** @var ReportSql $sql */
    protected $sql;

    /** @var array $mlses */
    private $mlses;

    public function __construct(CommandArguments $commandArguments)
    {
        $this->setConnection(DB::connection('rets'));
        $this->commandArguments = $commandArguments;
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
                array_push($data, $this->getConnection()->statement($this->sql));
            }
        } else {
            $data = $this->getConnection()->statement($this->sql);
        }

        return $data;
    }

    /**
     * @param ReportSql $sql
     */
    public function setSql(ReportSql $sql)
    {
        $this->sql = $sql->getSql();
    }

    /**
     * @param array $mlsOrgId
     */
    public function setMlses(array $mlsOrgId)
    {
        $this->mlses = $mlsOrgId;
    }
}
