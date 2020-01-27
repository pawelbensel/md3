<?php


namespace App\Services\Matcher;


use App\Models\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

abstract class BaseMatcher implements MatcherInterface
{
    /** @var Builder  */
    protected $queryBuilder;
    protected $fields = [];
    protected $rate = 0;
    protected $table;

    protected const AGENT = 'agent';
    protected const OFFICE = 'office';
    protected const PROPERTY = 'property';


    public function __construct()
    {
        $this->queryBuilder = DB::connection()->table($this->table.'s')->select($this->table.'s.id');
        $this->setBaseBuilder();
    }

    public function getMatchedBy()
    {
        return implode(', ',$this->fields);
    }

    public function getRate()
    {
        return $this->rate;
    }

    final private function setBaseBuilder()
    {
        $fields = $this->fields;
        array_walk($fields, function(&$field) {
            $field = $field.'s';
        });

        foreach ($fields as $field){
                $this->queryBuilder->leftJoin(
                    $this->table.'_'.$field,
                    $this->table.'s.id',
                    '=',
                    $this->table.'_'.$field.'.'.$this->table.'_id'
                );
        }
    }

    protected function isSatisfied(array $row){
        if(count($this->fields) > count(array_intersect($this->fields, array_keys($row)))){
            return false;
        }

        foreach ($this->fields as $field) {
            if(!isset($row[$field]) || empty($row[$field])){
                return false;
            }
        }

        return true;
    }
}
