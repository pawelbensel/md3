<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class ZAddress1CityPhoneMatcher extends BaseMatcher
{
    protected $fields = ['addresses', 'phone'];
    protected $rate = 50;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['city'])||
            !isset($row['address1'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder

            ->whereRaw('office_addresses.address1 = \''.$row['address1'].'\'')
            ->whereRaw('office_addresses.city = \''.$row['city'].'\'')
            ->whereRaw('office_phones.slug = \'' . $row['phone'] . '\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'address1, city, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}