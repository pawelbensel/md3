<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class LicenseNumberMatcher extends BaseMatcher
{
    protected $fields = ['license_number'];
    protected $rate = 100;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!array_key_exists('license_number', $row) ||
            empty($row['license_number']) ||
            strlen($row['license_number']) < 9
        ){
            return null;
        }

        $agent = $this->queryBuilder
            ->whereRaw('agent_license_numbers.license_number = \''. $row['license_number'].'\'')
            ->first();

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
