<?php


namespace App\Services\Merge;

use App\Models\Similar;
use App\OneManyModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface MergeServiceInterface
{
    public function mergre(Similar $similar): OneManyModel;

    public function discard(Similar $similar);

    public function revert(Similar $similar): OneManyModel;

    public function getSimilars(int $pageNumber): Collection;

    public function getHistory(int $pageNumber): Collection;
}