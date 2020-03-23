<?php


namespace App\Services\Report\Destination;


use App\Services\Report\Entity\ReportField;
use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Entity\ReportMap;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RetsReportDestination extends DatabaseReportDestination implements Mappable
{
    /** @var ReportMap $map */
    private $map;

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
        $sqlValues = [];
        foreach($data as $row) {
            $sqlRow = "('";
            $sqlRow .= implode("','", $row);
            $sqlRow .= "')".PHP_EOL;
            $sqlValues[] = $sqlRow;
        }

        $columns = $this->map->toString();

        $sql = "INSERT INTO $this->table $columns values ";
        $sql .= implode(',', $sqlValues);

        return $this->getConnection()->insert(DB::raw($sql));
    }

    public function getMap(): ReportMap
    {
        $map = new ReportMap([]);
        $columns =$this->getConnection()->select(DB::raw('DESCRIBE '.$this->table));
        foreach ($columns as $index => $column)
        {
            $map->addField(new ReportField((int)$index, $column->Field));
        }

        return $this->map = $map;
    }
}
