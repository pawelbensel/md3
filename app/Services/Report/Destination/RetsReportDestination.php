<?php


namespace App\Services\Report\Destination;


use Illuminate\Support\Facades\DB;

class RetsReportDestination extends DatabaseReportDestination
{
    public function __construct()
    {
        $this->setConnection(DB::connection('reports'));
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        // TODO: Implement store() method.
    }
}
