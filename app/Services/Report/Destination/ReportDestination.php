<?php


namespace App\Services\Report\Destination;


abstract class ReportDestination
{
    /**
     * @param array $data
     * @return mixed
     */
    abstract public function store(array $data);
}
