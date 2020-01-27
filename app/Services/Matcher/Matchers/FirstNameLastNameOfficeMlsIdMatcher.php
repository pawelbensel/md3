<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class FirstNameLastNameOfficeMlsIdMatcher extends BaseMatcher
{
    protected $fields = ['first_name', 'last_name'];
    protected $rate = 90;
    protected $table = self::AGENT;

    public function match(array $row): ?\stdClass
    {
        if(!$this->isSatisfied($row)){
            return null;
        };

        $agent = $this->queryBuilder
            ->leftJoin('agent_office','agent_office.agent_id','=','agent.id')
            ->leftJoin('office_mls_ids','office_mls_ids.office_id','=','agent_office.office_id')
            ->whereRaw('agent_first_names.first_name like \'%'.$row['first_name'].'%\'')
            ->whereRaw('agent_last_names.last_name like \'%'.$row['last_name'].'%\'')
            ->whereRaw("office_mls_ids.mls_id like '%".$row['office_mls_id']."%'")
            ->first();

        return $agent;
    }

    public function getMatchedBy()
    {
        return implode(', ', $this->fields).', office_id';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
