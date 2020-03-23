<?php


namespace App\Services\Report\Exceptions;


use Throwable;

class MappingException extends \Exception
{
    public function __construct(string $sourceField, string $mapField, Throwable $previous = null)
    {
        $message = "Field from source (".$sourceField.") does not match to provided report map field (".$mapField.").";
        $code = 0;
        parent::__construct($message, $code, $previous);
    }
}
