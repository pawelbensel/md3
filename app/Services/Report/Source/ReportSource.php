<?php


namespace App\Services\Report\Source;


use App\Services\Report\Entity\Data;
use Illuminate\Database\Connection;

abstract class ReportSource
{
    /**
     * @return mixed
     */
    public abstract function getData();
}
