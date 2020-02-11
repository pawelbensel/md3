<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeWebsite extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
