<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;

class AgentMlsId extends Model
{
    use Checkable;

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
