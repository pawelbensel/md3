<?php

namespace App\Services;
use SourceRowService;
use SourceObjectService;

class BaseService {

	protected $source;
	protected $mapArray;
    protected $checkedRow;

    public function setSource(string $source)
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

