<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use App\Services\PropertyService;
use Illuminate\Database\Eloquent\Model;

class P001_PropertyMlsIdMatcher extends BaseMatcher
{
    protected $fields = ['mls_id'];
    protected $rate = 95;
    protected $table = self::PROPERTY;

    public function match(array $row): ?Model
    {
        if (!isset($row['mls_id'])||
            empty($row['mls_id'])||
            !isset($row['mls_name']))
        {
            return null;
        }

        $property = $this->queryBuilder
            ->whereRaw('prop_mls_ids.mls_id = \''. $row['mls_id']. '\'')
            ->whereRaw('prop_mls_ids.mls_name = \''. $row['mls_name']. '\'')
            ->first();

        return $property;
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof PropertyService;
    }
}
