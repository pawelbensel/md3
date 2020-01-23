<?php

namespace App\Console\Commands;

use App\Models\Office;
use App\Services\AgentService;
use App\Services\OfficeService;
use App\Services\Source\DrBillSourceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DrBillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drbill:parse {--all }
                                        {--offset= : Db offset to start }
                                        {--limit= : DB Limit }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DrBill Parse';

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
        $source = new DrBillSourceService();
        
        if ($this->option('offset')) {
            $source->setOffset((int)$this->option('offset'));
        }

        if ($this->option('limit')) {
            $source->setLimit((int)$this->option('limit'));
        }

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

        $office = new OfficeService();
        $office->setSource($source->getSource());
        $agent = new AgentService();
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
                Log::channel('drbill')->error('Could not parse data', (array) $e);
            }
        }
    }
}