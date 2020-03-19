<?php


namespace App\Services\Report\SQL;


abstract class ReportSql
{
    /**
     * @var string
     */
    protected $sql = "";

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}
