<?php

namespace App\Console\Commands;

use App\Models\Office;
use App\Services\AgentService;
use Illuminate\Console\Command;
use App\Services\Source\RisSourceService ;
use App\Services\OfficeService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class RisCommmand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ris:parse {office_id? : Set scope of office id for the command }
                                       {--all }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $source = new RisSourceService();

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

    public function parseDataSegment($source)
    {
        $source->getData();
        $data = $source->parseData();

        $scopeOfficeId = $this->argument('office_id');

        $office = new OfficeService();
        $office->setSource($source->getSource());
        $agent = ($scopeOfficeId)? new AgentService($scopeOfficeId): new AgentService();
        $agent->setSource($source->getSource());

        foreach ($data as $row) {
            try {
                $office->setSourceRowId($row['source_row']['source_row_id']);
                $officeId = $office->getId($row['office']);
                $currentOffice = Office::find($officeId);

                $agent->setOffice($currentOffice);
                $agent->setSourceRowId($row['source_row']['source_row_id']);
                $agent->getId($row['agent']);
            }catch (\Exception $e){
                Log::channel('ris')->error('Could not parse data', (array) $e);
            }
        }
    }
}
