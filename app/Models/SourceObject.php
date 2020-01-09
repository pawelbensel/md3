<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceObject extends Model
{
    protected $fillable = ['source', 'object', 'hash', 'object_type'];
}
