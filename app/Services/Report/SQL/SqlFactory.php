<?php


namespace App\Services\Report\SQL;


use App\Console\Commands\CommandArguments;
use App\Services\Report\Source\ReportSource;
use ReflectionException;

class SqlFactory
{
    /**
     * @param CommandArguments $arguments
     * @return ReportSql
     * @throws ReflectionException
     */
    public static function factory(CommandArguments $arguments): ReportSql
    {
        $sql = SqlFactory::getSourceName($arguments->getOptions()['sql']);
        return new $sql($arguments);
    }

    /**
     * @param $source
     * @return string
     * @throws ReflectionException
     */
    private static function getSourceName($source)
    {
        $toRemove = ['_','-'];
        $className = str_replace($toRemove, '',ucwords($source, "\t\r\n\f\v\_\-" ));
        $namespace  = (new \ReflectionClass(ReportSql::class))->getNamespaceName();
        return $namespace.'\\'.$className;
    }
}
