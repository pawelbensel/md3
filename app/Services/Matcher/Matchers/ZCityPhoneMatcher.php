<?php

namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ZCityPhoneMatcher extends BaseMatcher
{
    protected $fields = ['addresses', 'phone'];
    protected $rate = 45;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['city'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('office_addresses.city = \''.$row['city'].'\'')
            ->whereRaw('office_phones.slug = \'' . $row['phone'] . '\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'city, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}