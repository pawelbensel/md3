<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class LicenseNumberMatcher extends BaseMatcher
{
    protected $rate = 60;
    protected $table = self::AGENT;

    public function match(array $row): ?\stdClass
    {
        if(!array_key_exists('license_number', $row)){
            return null;
        }
        $agent = $this->queryBuilder
            ->where('license_number', '=', $row['license_number'])->first();

        return $agent;
    }

    public function getMatchedBy()
    {
        return 'license_number';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
