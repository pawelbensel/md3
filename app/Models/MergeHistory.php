<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MergeHistory extends Model
{

    use SoftDeletes;

    public function target()
    {
        return $this->morphTo();
    }

    public function previous()
    {
        return $this->morphTo()->withTrashed();
    }

    public function similar()
    {
        return $this->belongsTo(Similar::class);
    }
}
