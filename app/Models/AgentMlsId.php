<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentMlsId extends Model
{
    use Checkable, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
