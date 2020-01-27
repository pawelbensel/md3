<?php


namespace App\Services\Matcher\Matchers;

use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class SlugOfficeNameCityPhoneMatcher extends BaseMatcher
{
    protected $fields = ['name', 'addresses', 'phone'];
    protected $rate = 90;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['name_slug'])||
            !isset($row['city'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('lower(office_names.slug) = \''.$row['name_slug'].'\'')
            ->whereRaw('lower(office_addresses.city) = \''.$row['city'].'\'')
            ->whereRaw('office_phones.slug = \''.$row['phone'].'\'')
            ->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'slug office_name, city, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
