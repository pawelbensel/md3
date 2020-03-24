<?php


namespace App\Services\Report\Entity;


use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Entity\ReportMap;

class MappedData extends Data implements Mappable
{

    public function getData()
    {
        // TODO: Implement getData() method.
    }

    public function getMap(): ReportMap
    {

    }
}
