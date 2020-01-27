<?php

namespace App\Services\Matcher\Matchers;

use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CleanSlugOfficeNameCityPhoneMatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses', 'phone'];
    protected $rate = 85;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['clean_name_slug'])||
            !isset($row['city'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('lower(office_names.slug) = \'' . $row['clean_name_slug'] . '\'')
            ->whereRaw('lower(office_addresses.city) = \'' . $row['city'] . '\'')
            ->whereRaw('office_phones.slug = \''.$row['phone'].'\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'clean slug office_name, city, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
