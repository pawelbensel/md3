<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LastUpdate extends Model
{
    protected $fillable = ['source', 'lastUpdateAt'];
}
