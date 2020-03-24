<?php


namespace App\Services\Report\Interfaces;


use App\Services\Report\Entity\ReportMap;

interface Mappable
{
    public function getMap(): ReportMap;
}
