<?php


namespace App\Services\Report\Source;

use Illuminate\Database\ConnectionInterface;

abstract class DatabaseReportSource extends ReportSource
{
    /** @var ConnectionInterface $connection */
    private $connection;


    public abstract function getData();

    /**
     * @param ConnectionInterface $connection
     */
    protected function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ConnectionInterface|null
     */
    protected function getConnection(): ?ConnectionInterface
    {
        return $this->connection;
    }
}
