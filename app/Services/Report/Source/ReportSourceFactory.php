<?php


namespace App\Services\Report\Source;


use App\Console\Commands\CommandArguments;
use App\Helpers\StringHelpers;
use ReflectionException;


class ReportSourceFactory
{
    /**
     * @param CommandArguments $arguments
     * @return ReportSource
     * @throws ReflectionException
     */
    public static function factory(CommandArguments $arguments): ReportSource
    {
       $source = ReportSourceFactory::getSourceName($arguments->getArguments()['source']);
       return new $source();
    }

    /**
     * @param $source
     * @return string
     * @throws ReflectionException
     */
    private static function getSourceName($source)
    {
        $toRemove = ['_','-'];
        $className = str_replace($toRemove, '',ucwords($source, "\t\r\n\f\v\_\-" ).'ReportSource');
        $namespace  = (new \ReflectionClass(ReportSource::class))->getNamespaceName();
        return $namespace.'\\'.$className;
    }
}
