<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class O006_SlugOfficeNameAddress1Citymatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses'];
    protected $rate = 90;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['name_slug'])||
            !isset($row['city'])||
            !isset($row['address1']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('office_names.slug = \''.$row['name_slug'].'\'')
            ->whereRaw('office_addresses.address1 = \''.$row['address1'].'\'')
            ->whereRaw('office_addresses.city = \''.$row['city'].'\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'slug office_name, address1, city';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
