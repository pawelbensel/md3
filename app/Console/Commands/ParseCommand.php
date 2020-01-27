<?php

namespace App\Console\Commands;

use App\Helpers\StringHelpers;
use App\Models\Office;
use App\Services\AgentService;
use App\Services\OfficeService;
use App\Services\ParseServiceFactory;
use App\Services\Source\MultiTableInterface;
use App\Services\Source\SourceFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ParseCommand extends Command
{
    private $agentService;
    private $officeService;

    protected $signature = 'parse { source : Type data source. }
                                   { --mls= : Type mls org_id }
                                   { --table= : Choose table to parse. (agents/offices/properties) } ';

    protected $description = 'Parsing data from choosen datasource to MegaData Database';

    public function __construct(OfficeService $officeService, AgentService $agentService)
    {
        parent::__construct();
        $this->officeService = $officeService;
        $this->agentService = $agentService;
    }

    public function handle()
    {
        try {
            $source = SourceFactory::factory($this->argument('source'), $this->options());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit(1);
        }
        $this->officeService->setSource($source);
        $this->agentService->setSource($source);


        if($source instanceof MultiTableInterface) {
            $parseService  = ParseServiceFactory::factory($this->option('table'));
            while($data = $source->getNextData()) {
                foreach ($data as $row) {
                    try {
                        $parseService->setSourceRowId($row['source_row']['source_row_id']);
                        $parseService->getId($row);
                    } catch (\Exception $e) {
                        Log::channel($this->argument('source'))->error('Could not parse data', (array) $e);
                    }
                }
            }
        }else {
            while($data = $source->getNextData()) {
                foreach ($data as $row){
                    try {
                        $this->officeService->setSourceRowId($row['source_row']['source_row_id']);
                        $officeId = $this->officeService->getId($row['office']);
                        $currentOffice = Office::find($officeId);

                        $this->agentService->setOffice($currentOffice);
                        $this->agentService->setSourceRowId($row['source_row']['source_row_id']);
                        $this->agentService->getId($row['agent']);


                    }catch (\Exception $e){
                       echo 'Exception';
                       Log::channel($this->argument('source'))->error('Could not parse data', (array) $e);
                    }
                }
            }
        }
    }
}
