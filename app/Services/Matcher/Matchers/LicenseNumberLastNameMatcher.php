<?php


namespace App\Services\Matcher\Matchers;


use App\Services\AgentService;
use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class LicenseNumberLastNameMatcher extends BaseMatcher
{
    protected $fields = ['license_number', 'last_name'];
    protected $rate = 100;
    protected $table = self::AGENT;

    public function match(array $row): ?Model
    {
        if(!array_key_exists('license_number', $row) ||
            empty($row['license_number']) ||
            !isset($row['last_name'])
        ){
            return null;
        }

        $agent = $this->queryBuilder
            ->whereRaw('agent_license_numbers.license_number = \''. $row['license_number'].'\'')
            ->whereRaw('agent_last_names.last_name like \'%'.$row['last_name'].'%\'')
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