<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use App\Services\PropertyService;
use Illuminate\Database\Eloquent\Model;

class StreetNameStreetNumberCityCountyMatcher extends BaseMatcher
{
    protected $fields = ['addresses'];
    protected $rate = 80;
    protected $table = self::PROPERTY;

    public function match(array $row): ?Model
    {
        if(
            !isset($row['city'])||
            !isset($row['county'])||
            !isset($row['street_name'])||
            !isset($row['street_number']))
        {
            return null;
        }

        $result = $this->queryBuilder
            ->whereRaw('prop_addresses.street_name = \''.$row['street_name'].'\'')
            ->whereRaw('prop_addresses.street_number = \''.$row['street_number'].'\'')
            ->whereRaw('prop_addresses.city = \''.$row['city'].'\'')
            ->whereRaw('prop_addresses.county = \''.$row['county'].'\'')
            ->get();
        //If more than one property returned
        if($result->count()>1){
            return null;
        }
        $property = $result->first();

        return $property;
    }

    public function getMatchedBy(): string
    {
        return 'county, city, street name';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof PropertyService;
    }
}
