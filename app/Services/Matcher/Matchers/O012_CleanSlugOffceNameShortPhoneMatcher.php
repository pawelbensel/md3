<?php


namespace App\Services\Matcher\Matchers;

use App\Services\Matcher\BaseMatcher;
use App\Services\OfficeService;
use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

class O012_CleanSlugOffceNameShortPhoneMatcher extends BaseMatcher
{
    protected $fields = ['name', 'phone'];
    protected $rate = 65;
    protected $table = self::OFFICE;

    public function match(array $row): ?Model
    {
        $tmpSql = '';

        if (!$this->isSatisfied($row)) {
            return null;
        };

        if (!isset($row['clean_name_slug'])||
            !isset($row['phone'])||
            !isset($row['short_phone_numbers'])
        ) {
            return null;
        }

        $this->queryBuilder
            ->whereRaw('office_names.slug = \'' . $row['clean_name_slug'] . '\'');            


        foreach ($row['short_phone_numbers'] as $shortNumber)
        {
            if ($tmpSql <> '') {
                $tmpSql . " OR";
            }
                $tmpSql .= " office_phones.slug like '%$shortNumber%' ";
            
        }
        $this->queryBuilder
             ->whereRaw("(".$tmpSql.")");
        
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
