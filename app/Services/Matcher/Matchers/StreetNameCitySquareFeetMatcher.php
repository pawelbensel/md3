<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\ParseServiceInterface;
use App\Services\PropertyService;
use Illuminate\Database\Eloquent\Model;

class StreetNameCitySquareFeetMatcher extends BaseMatcher
{
    protected $fields = ['addresses', 'square_feet'];
    protected $rate = 85;
    protected $table = self::PROPERTY;

    public function match(array $row): ?Model
    {
        if (!isset($row['square_feet']) ||
            !isset($row['city']) ||
            !isset($row['street_name'])) {
            return null;
        }

        $result = $this->queryBuilder
            ->whereRaw('prop_addresses.street_name = \'' . $row['street_name'] . '\'')
            ->whereRaw('prop_addresses.city = \'' . $row['city'] . '\'')
            ->whereRaw('prop_square_feets.square_feet = \'' . $row['square_feet'] . '\'')
            ->get();
        //If more than one property returned
        if ($result->count() > 1) {
            return null;
        }
        $property = $result->first();

        return $property;
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof PropertyService;
    }
}

