<?php


namespace App\Services\Report;


use App\Services\Report\Destination\ReportDestination;
use App\Services\Report\Source\ReportSource;

interface ReportServiceInterface
{
    public function generete();
}
