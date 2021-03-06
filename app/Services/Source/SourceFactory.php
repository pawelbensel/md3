<?php


namespace App\Services\Source;

use App\Services\Source\MultiTableInterface as MultiTableInterface;


class SourceFactory
{
    public static function factory(string $source, array $options): SourceInterface
    {
        $className = SourceFactory::getSourceName($source);

        if(!class_exists($className)) {
            throw new \InvalidArgumentException('Unsupported source given.');
        }

        $implementations = class_implements($className);

        if(isset($implementations[__NAMESPACE__.'\\'.'MultiTableInterface'])){
            if(!isset($options['table'])) {
                throw new \InvalidArgumentException('--table option is required while multitable source choosen.');
            }
            $source = new $className($options['table']);
        } else {
            $source = new $className();
        }

        if (isset($options['offset'])) {
            $source->setOffset((int)$options['offset']);
        }

        if (isset($options['limit'])) {
            $source->setLimit((int)$options['limit']);
        }

        if (isset($options['update'])) {
            $source->setUpdate($options['update']);
        }

        return $source;
    }

    private static function getSourceName(string $source): string
    {
        $toRemove = ['_','-'];
        $className = str_replace($toRemove, '',ucwords($source, "\t\r\n\f\v\_\-" ).'SourceService');
        return __NAMESPACE__.'\\'.$className;
    }
}
