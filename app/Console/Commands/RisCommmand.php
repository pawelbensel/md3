<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Source\RisSourceService as Source;
use App\Services\OfficeService;

class RisCommmand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ris:parse';

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

        $source = new Source();
        $source->getData();
        $data = $source->parseData(); 
        $office = new OfficeService();
        $office->setSource('ris_media');
        
        foreach ($data as $row) {
            dump($row['office']);
            $office->getId($row['office']);
        }
    }
}
