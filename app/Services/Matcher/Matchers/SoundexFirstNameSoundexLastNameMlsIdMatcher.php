<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class SoundexFirstNameSoundexLastNameMlsIdMatcher extends BaseMatcher
{
    protected $fields = ['first_name', 'last_name', 'mls_id'];
    protected $rate = 60;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!$this->isSatisfied($row)){
            return null;
        };

        $agent = $this->queryBuilder
            ->whereRaw("levenshtein_ratio('".\Str::slug($row['first_name'],'')."', agent_first_names.slug) >80")
            ->whereRaw("levenshtein_ratio('".\Str::slug($row['last_name'],'')."', agent_last_names.slug) >80")
            ->whereRaw("agent_mls_ids.mls_id like '%".$row['mls_id']."%'")
            ->first();

        return $agent;
    }

    public function getMatchedBy(): string
    {
        $matchedBy = $this->fields;
        $matchedBy[0] = 'soundex '.$matchedBy[0];
        $matchedBy[1] = 'soundex '.$matchedBy[1];

        return implode(', ',$matchedBy);
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
