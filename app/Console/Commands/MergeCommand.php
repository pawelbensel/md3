<?php

namespace App\Console\Commands;

use App\Services\Merge\MergeServiceInterface;
use Illuminate\Console\Command;

class MergeCommand extends Command
{
    private $mergeService;

    protected $signature = 'merge { object_id : Main object_id }
                                  { similar_id : Similar object id }
                                  { --type= : Type of objects to merge } ';

    protected $description = 'Parsing data from choosen datasource to MegaData Database';

    public function __construct(MergeServiceInterface $mergeService)
    {
        parent::__construct();
        $this->mergeService = $mergeService;
    }

    public function handle()
    {
        $this->mergeService->getSimilars(0);
    }
}