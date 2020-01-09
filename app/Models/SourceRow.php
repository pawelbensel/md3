<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceRow extends Model
{
    protected $fillable = ['source', 'row', 'hash'];
}
