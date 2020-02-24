<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropPrice extends Model
{
    public function status()
    {
        return $this->belongsTo(Transaction::class);
    }
}
