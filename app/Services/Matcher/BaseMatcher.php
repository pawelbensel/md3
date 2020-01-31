<?php


namespace App\Services\Matcher;


use App\Models\Agent;
use App\Models\Office;
use App\Models\Prop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

abstract class BaseMatcher implements MatcherInterface
{
    /** @var Builder  */
    public $queryBuilder;
    protected $fields = [];
    protected $rate = 0;
    protected $table;

    protected const AGENT = 'agent';
    protected const OFFICE = 'office';
    protected const PROPERTY = 'prop';


    public function __construct()
    {
        switch($this->table) {
            case self::AGENT: $this->queryBuilder = Agent::query()->select('agents.*'); break;
            case self::OFFICE: $this->queryBuilder = Office::query()->select('offices.*'); break;
            case self::PROPERTY: $this->queryBuilder = Prop::query()->select('props.*'); break;
        }
        $this->setBaseBuilder();
    }

    public function getMatchedBy(): string
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
            // if fields ends with s like adresses.
            $field = (substr($field, -strlen('s')) === 's')? $field: $field.'s';
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
        $sameKeys = count(array_intersect($this->fields, array_keys($row)));
        if(array_intersect(['city', 'address1', 'address2'], array_keys($row))){
            $sameKeys += 1;
        }
        if(count($this->fields) > $sameKeys){
            return false;
        }

        foreach ($this->fields as $field) {
            if($field == 'addresses'){
                continue;
            }
            if(!isset($row[$field]) || empty($row[$field])){
                return false;
            }
        }

        return true;
    }
}
