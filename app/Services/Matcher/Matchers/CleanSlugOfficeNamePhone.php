<?php


namespace App\Services\Matcher\Matchers;


use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CleanSlugOfficeNamePhone extends BaseMatcher
{
    protected $fields = ['name', 'phone'];
    protected $rate = 70;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if(!isset($row['clean_name_slug'])||
            !isset($row['phone']))
        {
            return null;
        }

        $office = $this->queryBuilder
            ->whereRaw('lower(office_names.slug) = \'' . $row['clean_name_slug'] . '\'')
            ->whereRaw('office_phones.slug = \'' . $row['phone'] . '\'')
            ->first();

        return $office;
    }

    public function getMatchedBy():string
    {
        return 'clean slug office_name, phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
