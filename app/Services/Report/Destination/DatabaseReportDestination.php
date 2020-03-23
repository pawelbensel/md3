<?php


namespace App\Services\Report\Destination;


use App\Services\Report\Interfaces\TableDestination;
use Illuminate\Database\ConnectionInterface;

abstract class DatabaseReportDestination extends ReportDestination implements TableDestination
{
    /** @var ConnectionInterface $connection */
    protected $connection;
    /** @var string $table */
    protected $table;

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

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }
}
