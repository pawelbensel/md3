<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;

class AgentPhone extends Model
{
    use Checkable;

    protected $fillable = ['phone'];
}
