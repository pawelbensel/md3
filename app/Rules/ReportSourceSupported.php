<?php

namespace App\Rules;

use App\Services\Report\Source\ReportSource;
use App\Services\Source\SourceFactory;
use Illuminate\Contracts\Validation\Rule;

class ReportSourceSupported implements Rule
{
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
        $className = $this->getSourceName($value);
        return class_exists($className);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Unsupported report source.';
    }

    private function getSourceName($source)
    {
        $toRemove = ['_','-'];
        $className = str_replace($toRemove, '',ucwords($source, "\t\r\n\f\v\_\-" ).'ReportSource');
        $namespace  = (new \ReflectionClass(ReportSource::class))->getNamespaceName();
        return $namespace.'\\'.$className;
    }
}
