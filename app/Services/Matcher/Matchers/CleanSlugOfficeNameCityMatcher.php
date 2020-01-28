<?php

namespace App\Services\Matcher\Matchers;

use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CleanSlugOfficeNameCityMatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses'];
    protected $rate = 65;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['clean_name_slug'])||
            !isset($row['city']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('office_names.slug = \'' . $row['clean_name_slug'] . '\'')
            ->whereRaw('office_addresses.city = \'' . $row['city'] . '\'')
            ->first();

        return $office;
    }

    public function getMatchedBy():string
    {
        return 'clean slug office_name, city';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
