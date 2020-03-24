<?php


namespace App\Services\Report\Exceptions;


use Throwable;

class NumberOfColumnsException extends \Exception
{
    public function __construct($message = "Source number of columns does not match to destination.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
