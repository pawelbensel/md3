<?php


namespace App\Services\Report\Interfaces;


use App\Services\Report\SQL\ReportSql;

interface SqlInjectable
{
    public function setSql(ReportSql $sql);
}
