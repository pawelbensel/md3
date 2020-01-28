<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class LicenseNumberMatcher extends BaseMatcher
{
    protected $rate = 60;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!array_key_exists('license_number', $row) || empty($row['license_number'])){
            return null;
        }
        $agent = $this->queryBuilder
            ->where('license_number', '=', $row['license_number'])->first();

        return $agent;
    }

    public function getMatchedBy(): string
    {
        return 'license_number';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof AgentService;
    }
}
