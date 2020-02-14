<?php

namespace App\Traits;

use App\Models\MergeHistory;

trait Historable
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function actual()
    {
        return $this->morphMany(MergeHistory::class, 'target', 'target_type', 'target_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function previous()
    {
        return $this->morphMany(MergeHistory::class, 'previous', 'previous_type', 'previous_id');
    }
}