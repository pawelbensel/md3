<?php


namespace App\Services\Report;


use App\Services\Report\Destination\ReportDestination;
use App\Services\Report\Source\ReportSource;

class ReportService implements ReportServiceInterface
{
    /** @var ReportSource $reportSource */
    private $reportSource;
    /** @var ReportDestination $reportDestination */
    private $reportDestination;

    public function __construct(ReportSource $reportSource, ReportDestination $reportDestination)
    {
        $this->reportSource = $reportSource;
        $this->reportDestination = $reportDestination;
    }

    public function generete()
    {
        $data = $this->reportSource->getData();
        $this->reportDestination->store($data);
    }
}
