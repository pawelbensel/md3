<?php

namespace App\Models;

use App\Traits\Historable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropMlsOfficeId extends Model
{
    use SoftDeletes, Historable;
    protected $dates = ['deleted_at'];
}
