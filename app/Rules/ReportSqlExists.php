<?php

namespace App\Rules;

use App\Services\Report\SQL\ReportSql;
use Illuminate\Contracts\Validation\Rule;
use ReflectionException;

class ReportSqlExists implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws ReflectionException
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
        return 'Sql does not exists.';
    }

    /**
     * @param $source
     * @return string
     * @throws ReflectionException
     */
    private static function getSourceName($source)
    {
        $toRemove = ['_','-'];
        $className = str_replace($toRemove, '',ucwords($source, "\t\r\n\f\v\_\-" ).'ReportSql');
        $namespace  = (new \ReflectionClass(ReportSql::class))->getNamespaceName();
        return $namespace.'\\'.$className;
    }
}
