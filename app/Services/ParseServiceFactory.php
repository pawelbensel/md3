<?php


namespace App\Services;


use App\Helpers\StringHelpers;
use App\Services\Source\MultiTableInterface;
use App\Services\Source\SourceInterface;

class ParseServiceFactory
{
    private const AGENT_TABLE_POSTIFX = 'agent';
    private const OFFICE_TABLE_POSTFIX = 'office';
    private const PROPERTIES_TABLE_POSTFIX = 'prop';

    public static function factory(string $table): ParseServiceInterface
    {
        if(StringHelpers::contains($table, self::AGENT_TABLE_POSTIFX)){
            return new AgentService();
        }
        if(StringHelpers::contains($table, self::OFFICE_TABLE_POSTFIX)){
            return new OfficeService();
        }
        if(StringHelpers::contains($table, self::PROPERTIES_TABLE_POSTFIX)){
            return new PropertyService();
        }

        throw new \Exception('Table name should contain agent or office');
    }
}
