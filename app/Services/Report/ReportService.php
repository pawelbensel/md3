<?php


namespace App\Services\Report;


use App\Services\Report\Destination\ReportDestination;
use App\Services\Report\Entity\ReportMap;
use App\Services\Report\Exceptions\MappingException;
use App\Services\Report\Exceptions\NumberOfColumnsException;
use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Interfaces\SqlInjectable;
use App\Services\Report\Source\ReportSource;
use App\Services\Report\SQL\ReportSql;
use function GuzzleHttp\Promise\queue;

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
        if (!$this->reportSource instanceof SqlInjectable ) {
            $sql = null;
        }
        $sql = $this->reportSource->getSql();

        if($sql != null && (!$sql instanceof Mappable)) {
            $sourceMap = null;
        }
        $sourceMap = $sql->getMap();

        if(!$this->reportDestination instanceof Mappable){
            $destinationMap = null;
        }
        $destinationMap = $this->reportDestination->getMap();

        $transformedData = [];
        if($sql instanceof ReportSql && $sourceMap instanceof ReportMap && $destinationMap instanceof ReportMap)
        {
            if($sourceMap->count() != $destinationMap->count())
            {
                throw new NumberOfColumnsException();
            }
            foreach ($data as $row) {

                $transformedRow = [];
                for($index = 0; $index < $sourceMap->count(); $index++){
                    try{
                        $key = $destinationMap->getField($index)->getFieldName();
                        $value = ((array)$row)[$sourceMap->getField($index)->getFieldName()];
                    } catch (\ErrorException $e) {
                        throw new MappingException($sourceMap->getField($index)->getFieldName(), $destinationMap->getField($index)->getFieldName());
                    }
                    $transformedRow[$key] = $value;
                }
                $transformedData[] = $transformedRow;
            }
        }

        $this->reportDestination->store($transformedData);
    }
}
