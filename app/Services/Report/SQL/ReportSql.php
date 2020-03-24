<?php


namespace App\Services\Report\SQL;


use App\Console\Commands\CommandArguments;

abstract class ReportSql
{
    /** @var CommandArguments $commandArguments */
    private $commandArguments;
    /** @var string $sql */
    protected $sql = "";

    public function __construct(CommandArguments $commandArguments)
    {
        $this->commandArguments = $commandArguments;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}
