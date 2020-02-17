<?php

namespace App\Console\Commands;

use App\Models\Similar;
use App\Services\Merge\MergeServiceInterface;
use Illuminate\Console\Command;

class MergeCommand extends Command
{
    private $mergeService;

    protected $signature = 'merge { similar_id : Id of object of class Similar }';

    protected $description = 'Merge two object in MegaData in once.';

    public function __construct(MergeServiceInterface $mergeService)
    {
        parent::__construct();
        $this->mergeService = $mergeService;
    }

    public function handle()
    {
        $similar = Similar::findOrFail($this->argument('similar_id'));
        $this->mergeService->mergre($similar);
    }
}
