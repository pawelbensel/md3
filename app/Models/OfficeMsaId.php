<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeMsaId extends Model
{
    use Checkable, SoftDeletes;
    protected $dates = ['deleted_at'];
}
