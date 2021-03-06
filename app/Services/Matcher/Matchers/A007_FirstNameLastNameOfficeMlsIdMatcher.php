<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class A007_FirstNameLastNameOfficeMlsIdMatcher extends BaseMatcher
{
    protected $fields = ['first_name', 'last_name'];
    protected $rate = 90;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!isset($row['first_name'])||
            !isset($row['last_name'])||
            !isset($row['office_mls_id'])||
            !isset($row['mls_name']))
        {
            return null;
        }

        $agent = $this->queryBuilder
            ->leftJoin('agent_office','agent_office.agent_id','=','agents.id')
            ->leftJoin('office_mls_ids','office_mls_ids.office_id','=','agent_office.office_id')
            ->whereRaw('agent_first_names.first_name like \'%'.$row['first_name'].'%\'')
            ->whereRaw('agent_last_names.last_name like \'%'.$row['last_name'].'%\'')            
            ->whereRaw("office_mls_ids.mls_id = '".$row['office_mls_id']."' and office_mls_ids.mls_name ='".$row['mls_name']."'")
            ->first();

        return $agent;
    }

    public function getMatchedBy(): string
    {
        return implode(', ', $this->fields).', office_id';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
