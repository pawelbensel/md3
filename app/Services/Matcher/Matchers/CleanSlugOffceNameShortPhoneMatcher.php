<?php


namespace App\Services\Matcher\Matchers;

use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CleanSlugOffceNameShortPhoneMatcher extends BaseMatcher
{
    protected $fields = ['name', 'phone'];
    protected $rate = 65;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        if (!$this->isSatisfied($row)) {
            return null;
        };

        if (!isset($row['clean_name_slug'])) {
            return null;
        }

        $this->queryBuilder
            ->whereRaw('lower(office_names.slug) = \'' . $row['clean_name_slug'] . '\'')
            ->whereRaw('office_phones.slug = \'' . $row['phone'] . '\'');

        foreach ($row['short_phone_numbers'] as $shortNumber)
        {
            $this->queryBuilder->orWhereRaw("office_phones.slug like '%$shortNumber%'");
        }

        $office = $this->queryBuilder->first();

        return $office;
    }

    public function getMatchedBy(): string
    {
        return 'clean slug office_name, short phone';
    }

    public function supports(ParseServiceInterface $parseService): bool
    {
        return $parseService instanceof OfficeService;
    }
}
