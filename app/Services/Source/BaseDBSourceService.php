<?php

namespace App\Services\Source;

use Illuminate\Support\Facades\DB;

class BaseDBSourceService extends BaseSourceService
{

    protected $limit = 100;
    protected $offset = 0;
    protected $tableName;
    protected $dbConnection;
    protected $tableCounter = null;
    protected $data;

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

    public function getData()
    {
        return $this->data = DB::connection($this->dbConnection)->table($this->tableName)->skip($this->offset)->take($this->limit)->get();
    }

    public function next()
    {
        $this->setOffset($this->offset + $this->limit);
    }

    public function getSegmentMaxIndex()
    {
        if($this->offset== 0){
            return $this->limit;
        }

        return $this->offset+$this->limit;
    }

}
