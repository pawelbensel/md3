<?php

namespace App\Services;

use App\Services\Source\SourceInterface;

class BaseService {

	protected $source;
    protected $checkedRow;

    public function setSource(SourceInterface $source)
    {
        $this->source = $source;
    }

	public function parseRow()
    {
        return $this->checkedRow['parse_source_row'];
    }

    public function parseObject()
    {
        return $this->checkedRow['parse_object'];
    }

}

