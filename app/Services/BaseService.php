<?php

namespace App\Services;

class BaseService {

	protected $source;

    public function setSource(string $source)
    {
        $this->source = $source;
    }

} 

