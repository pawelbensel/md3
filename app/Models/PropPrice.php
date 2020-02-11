<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropPrice extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function status()
    {
        return $this->belongsTo(PropStatus::class);
    }
}
