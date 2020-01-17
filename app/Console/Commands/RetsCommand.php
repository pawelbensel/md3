<?php

namespace App\Console\Commands;


use App\Services\AgentService;
use App\Services\OfficeService;
use App\Services\Source\RetsSourceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetsCommand extends Command
{
    protected $signature = 'rets:parse {org_id : Parse data for selected org_id } 
                                       {target : Parse agents or offices }
                                       {--all }';

    protected $description = 'Parsing agents or offices from RETS database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $source = new RetsSourceService($this->argument('org_id'), $this->argument('target'));

        if($this->option('all')){
            while($source->getCounter()>$source->getSegmentMaxIndex())
            {
                $this->parseDataSegment($source);
                $source->next();
            }
        } else {
            $this->parseDataSegment($source);
        }
    }

    private function parseDataSegment($source)
    {
        $source->getData();
        $data = $source->parseData();

        if ($this->argument('target') == 'agents') {
            $parseService = new AgentService();
            $parseService->setSource($source->getSource());
            $parseService->setMlsName($this->argument('org_id'));
        }
        if ($this->argument('target') == 'offices') {
            $parseService = new OfficeService();
            $parseService->setSource($source->getSource());
            $parseService->setMlsName($this->argument('org_id'));
        }

        foreach ($data as $row) {
            try {
                $parseService->setSourceRowId($row['source_row']['source_row_id']);
                $parseService->setMlsName($this->argument('org_id'));
                if($parseService instanceof AgentService){
                    $parseService->getId($row['agent']);
                }
                if($parseService instanceof OfficeService){
                    $parseService->getId($row['office']);
                }
            } catch (\Exception $e) {
                Log::channel('rets')->error('Could not parse data', (array) $e);
            }
        }
    }
}
