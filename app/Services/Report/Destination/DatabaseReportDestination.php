<?php


namespace App\Services\Report\Destination;


use Illuminate\Database\ConnectionInterface;

abstract class DatabaseReportDestination extends ReportDestination
{
    /** @var ConnectionInterface $connection */
    private $connection;

    /**
     * @param array $data
     * @return mixed
     */
    public abstract function store(array $data);

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
