<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class FirstNameLastNameMlsIdMatcher extends BaseMatcher
{
    protected $fields = ['first_name', 'last_name', 'mls_id'];
    protected $rate = 100;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!isset($row['first_name'])||
            !isset($row['last_name'])||
            !isset($row['mls_id'])||
            !isset($row['mls_name']))
        {
            return null;
        }

        $agent = $this->queryBuilder
            ->whereRaw('agent_first_names.first_name like \'%'.$row['first_name'].'%\'')
            ->whereRaw('agent_last_names.last_name like \'%'.$row['last_name'].'%\'')
            ->whereRaw("agent_mls_ids.mls_id like '%".$row['mls_id']."%'")
            ->whereRaw("agent_mls_ids.mls_id like '%".$row['mls_id']."%' and agent_mls_ids.mls_name='".$row['mls_name']."'")
            ->first();

        return $agent;
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
