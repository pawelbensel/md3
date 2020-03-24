<?php


namespace App\Services\Report\Exceptions;


use Throwable;

class InvalidOrderException extends \Exception
{
    public function __construct($message = "Invalid report field order index.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
