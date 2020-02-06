<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class O004_OfficeNameCityPhoneMatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses', 'phone'];
    protected $rate = 95;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['name'])||
            !isset($row['city'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('office_names.name = \''.$row['name'].'\'')
            ->whereRaw('office_addresses.city = \''.$row['city'].'\'')
            ->whereRaw('office_phones.slug = \''.$row['phone'].'\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'office_name, city, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
