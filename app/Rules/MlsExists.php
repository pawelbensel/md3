<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MlsExists implements Rule
{
    /** @var array $notFoundMlses */
    private $notFoundMlses = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value === 'all') {
            return true;
        }

        $orgIds = DB::connection('rets')
            ->table('orgNameId')
            ->select(['org_id'])
            ->whereIn('org_id', $value)
            ->get();
        $orgIds = $orgIds->map(function($item){
            return $item->org_id;
        })->toArray();
        $passes = true;
        foreach ($value as $signleMls) {
            if(!in_array($signleMls, $orgIds)){
                array_push($this->notFoundMlses, $signleMls);
                $passes = false;
            }
        }

        return $passes;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'These provided mls ids ['.implode(',',$this->notFoundMlses).'] could not be found.';
    }
}
