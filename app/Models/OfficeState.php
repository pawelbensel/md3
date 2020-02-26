<?php

namespace App\Models;

use App\Traits\Checkable;
use App\Traits\Historable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeState extends Model
{
    use Checkable, SoftDeletes, Historable;
}
