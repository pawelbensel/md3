<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class O001_OfficeNameAddress1Address2CityMatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses'];
    protected $rate = 100;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['name'])||
            !isset($row['city'])||
            !isset($row['address1'])||
            !isset($row['address2']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('office_names.name = \''.$row['name'].'\'')
            ->whereRaw('office_addresses.address1 = \''.$row['address1'].'\'')
            ->whereRaw('office_addresses.address2 = \''.$row['address2'].'\'')
            ->whereRaw('office_addresses.city = \''.$row['city'].'\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'office_name, address1, address2, city';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
