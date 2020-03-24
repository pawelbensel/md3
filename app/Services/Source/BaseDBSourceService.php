<?php

namespace App\Services\Source;

use App\Models\LastUpdate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaseDBSourceService extends BaseSourceService implements SourceInterface
{

    protected $limit = 100;
    protected $offset = 0;
    protected $tableName;
    protected $dbConnection;
    protected $tableCounter = null;
    protected $data;
    protected $update;
    protected $lastUpdateAt;
    protected $updateIdentifier;


    public function setUpdate(bool $update)
    {
        $this->update = $update;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function setDBConnection(string $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getCounter(?string $tableName = null)
    {
        $table = $tableName ?? $this->tableName;
        return DB::connection($this->dbConnection)->table($table)->count();
    }

    public function getNextData(): ?array
    {
        $returnArray = array();
        $this->lastUpdateAt = LastUpdate::query()->firstWhere(['source' => $this->source]);

        $queryBuilder = DB::connection($this->dbConnection)
            ->table($this->tableName)
            ->selectRaw('*, CASE WHEN(updtime>timestamp) THEN updtime ELSE timestamp END as ts')
            ->orderBy('ts','ASC')
            ->skip($this->offset)
            ->take($this->limit);


        if($this instanceof RetsSourceService && $this->update && $this->lastUpdateAt){
            $queryBuilder->havingRaw('ts >= :last_update', ['last_update'=> $this->lastUpdateAt->value('lastUpdateAt')]);
        }

        $this->data = $queryBuilder->get();

        if($this instanceof RetsSourceService && ($this->data->count()>0)) {
            $updateDateTime = $this->getSegmentUpdateTime();
            if($updateDateTime > (isset($this->lastUpdateAt->lastUpdateAt)?$this->lastUpdateAt->lastUpdateAt:null)){
                $this->lastUpdateAt = LastUpdate::updateOrCreate(['source' => $this->source], ['source' => $this->source, 'lastUpdateAt' => $updateDateTime]);
            }
        }

        foreach ($this->data as $row) {
            $returnArray[] = $this->map($row);
        }
        $this->offset += $this->limit;

        return $returnArray;
    }

    private function getSegmentUpdateTime()
    {
        return $this->data->first(function($value, $key){
            return $key == 'ts' && $value != null;
        })->ts;
    }
}
