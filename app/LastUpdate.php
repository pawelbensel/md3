<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastUpdate extends Model
{
    protected $fillable = ['source', 'lastUpdateAt'];
}
