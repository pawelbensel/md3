<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;

class QueryGeneratorService
{

    private $groupBy = [];
    private $operators = [];
    private $fields = [];
    private $queryObject;
    
    
        
    private function mapToTimeOfADay($field) {
        
        $timeOfADay = DB::raw("(case when to_char(".$field.", 'HH24:MI') BETWEEN '06:00' and '12:00'
            then 'MORNING'
            when to_char(".$field.", 'HH24:MI') BETWEEN '12:01' and '18:00'
            then 'AFTERNOON'
            when to_char(".$field.", 'HH24:MI') BETWEEN '18:01' and '22:00'
            then 'EVENING'
            else 'NIGHT'
            end) ");

        return $timeOfADay;

    }

    private function setFields() {
        $this->fields['day_of_week'] = ['field' => DB::raw("TO_CHAR(UTC_START_TIME, 'D')")];
    }

    private function getField($fieldLabel) {
        if (isset($this->fields[$fieldLabel]['field'])) {
            return $this->fields[$fieldLabel]['field'];
        }
        else
            return $fieldLabel;

    }

    private function setOperators() {
        $this->operators['='] = ['operator' => '='];
        $this->operators['in'] = ['operator' => ''];
        $this->operators['>'] = ['operator' => '>'];
        $this->operators['>='] = ['operator' => '>='];
        $this->operators['<'] = ['operator' => '<'];
        $this->operators['<='] = ['operator' => '<='];
        $this->operators['<>'] = ['operator' => '<>'];
        $this->operators['equal to'] = $this->operators['='];
        $this->operators['contains'] = ['operator' => 'like', 'pre'=>'%', 'post'=>'%'];
        $this->operators['does not contain'] = ['operator' => 'not like', 'pre'=>'%', 'post'=>'%'];
        $this->operators['starts with'] = ['operator' => 'like', 'post'=>'%'];
        $this->operators['ends with'] = ['operator' => 'like', 'pre'=>'%'];
        $this->operators['is empty'] = ['operator' => '=', 'value'=>''];
        $this->operators['is not empty'] = ['operator' => '<>', 'value'=>''];        
        $this->operators['at least'] = $this->operators['>='];
        $this->operators['less then'] = $this->operators['<'];
        $this->operators['less or equal to'] = $this->operators['<='];
        $this->operators['more then'] = $this->operators['>'];
        $this->operators['more or equal to'] = $this->operators['>='];
        
    }

    private function setGroupBy() {
        $this->groupBy['UTC date'] = ['field' => "TO_CHAR(UTC_START_TIME, 'YYYY-MM-DD')"];
        $this->groupBy['Human date'] = ['field' => "TO_CHAR(HUMAN_START_TIME, 'YYYY-MM-DD')"];
        $this->groupBy['UTC Day of week'] = ['field' => "TO_CHAR(UTC_START_TIME, 'day')"];
        $this->groupBy['Human Day of week'] = ['field' => "TO_CHAR(HUMAN_START_TIME, 'day')"];
        $this->groupBy['Webrowser'] = ['field' => 'BROWSER'];
        $this->groupBy['Country'] = ['field' => 'COUNTRY'];
        $this->groupBy['City'] = ['field' => 'CITY'];        
        $this->groupBy['Operating system'] = ['field' => 'PLATFORM'];
        $this->groupBy['Device'] = ['field' => 'DEVICE_TYPE'];
        $this->groupBy['Weather'] = ['field' => 'icon'];        
        $this->groupBy['Webpage they came'] = ['field' => 'HTTP_REFERER'];
        $this->groupBy['Website they visit'] = ['field' => 'URI'];
        $this->groupBy['Time they spent'] = ['field' => 'SPEND_TIME'];
        $this->groupBy['People status'] = ['field' => 'BBB_STATUS'];
        $this->groupBy['Time of a day'] = ['field' => $this->mapToTimeOfADay('HUMAN_START_TIME')];        
        
    }

    public function __construct()
    {
        $this->setGroupBy();
        $this->setOperators();
        $this->setFields();

    }

    public function slug(string $text) {
        $result = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        return strtolower(trim($result));
    }

    public function getGroupByOptions()
    {   
        foreach ($this->groupBy as $label => $fieldName) {            
            $returnAarray[] = ['name' => $label, 'field' => $this->slug($label)];
        }
        return $returnAarray ?? [];
    }    

    public function getGroupByField($label)
    {
        return $this->groupBy[$label]['field'];
    }
    
    private function getOperator($label)
    {
        return $this->operators[$label]['operator'];
    }

    private function getParsedValue($value, $operator)
    {
        
        if (isset($this->operators[$operator]['field']))
            return $this->operators[$operator]['field'];
        if (isset($this->operators[$operator]['pre']))
            $value = $this->operators[$operator]['pre'].$value;
        if (isset($this->operators[$operator]['post']))
            $value .= $this->operators[$operator]['post'];
        if ($operator=='in') {
            $value = implode($value);
        }
        return $value;
    }

    private function parseQBRule($query, $rule, $method) {
        $operator = $rule['query']['selectedOperator'];
        $value = $rule['query']['value'];
        $field = $rule['query']['rule'];
        
        if ($rule['query']['selectedOperator'] <> 'in') {
            $query->{$method}($this->getField($field), $this->getOperator($operator), $this->getParsedValue($value, $operator));
        } else  {            
            $query->whereIn($this->getField($field), $this->getParsedValue($value, $operator));

        }
                
    }

    private function parseQBGroup($query, $group, $method = 'where')
    {
        $query->{$method}(function ($subquery) use ($group) {
            $sub_method = $group['query']['logicalOperator'] === 'all' ? 'where' : 'orWhere';
            foreach ($group['query']['children'] as $child) {
                if ($child['type'] === 'query-builder-group') {
                    $this->parseQBGroup($subquery, $child, $sub_method);
                } else {
                    $this->parseQBRule($subquery, $child, $sub_method);
                }
            }
        });
    }

    public function getAnswers($queryObject, $query) {
        if ($query) {
            $group = [];
            $group['query'] = ['children' => $query['children']];
            $group['query']['logicalOperator'] = $query['logicalOperator'];    
            $method = $query['logicalOperator'] === 'all' ? 'where' : 'orWhere';
            $this->parseQBGroup($queryObject, $group, $method);
        }
        
    }
}
