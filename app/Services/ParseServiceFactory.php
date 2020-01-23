<?php


namespace App\Services;


use App\Services\Source\MultiTableInterface;
use App\Services\Source\SourceInterface;

class ParseServiceFactory
{
    public static function factory(SourceInterface $source, array $options = [])
    {
        if($source instanceof MultiTableInterface){

        }
    }
}
