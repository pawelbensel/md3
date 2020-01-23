<?php

namespace App\Services\Source;
use App\Services\SourceRowService;
use App\Services\SourceObjectService;
use Illuminate\Database\Eloquent\Collection;

class BaseSourceService implements SourceInterface
{

    protected $source;
    protected $mapArray;

    protected $sourceRowService;
    protected $sourceObjectService;

    /**
     * set sources and set up source row and object services to get IDs from database
     *
     * @var string
     */
    public function setSource(string $source)
    {
        $this->source = $source;
        $this->sourceRowService = new SourceRowService($source);
        $this->sourceObjectService = new SourceObjectService($source);
    }

    /**
     * map row for the final aray structure base on mapArray variable
     *
     * @var string
     */

    public function map($row){
        foreach($this->mapArray as $objectType => $objectMap) {
            $sourceData = [];
            foreach ($objectMap as $destinationName => $sourceName) {
                if (isset($row->$sourceName) && ($row->$sourceName<>'')) {
                    $returnArray[$objectType][$destinationName] = $row->$sourceName;
                } else
                    $returnArray[$objectType][$destinationName] = null;
            }
            $so = $this->sourceObjectService->get($returnArray[$objectType], $objectType);
            $sourceData['source_object_id'] = $so->id;
            $sourceData['parse_source_object'] = !$so->parsed;
            $returnArray[$objectType]['source_object'] = $sourceData;
        }

        $sr = $this->sourceRowService->get($row);
        $sourceData = [];
        $sourceData['source'] = $this->source;
        $sourceData['source_row_id'] = $sr->id;
        $sourceData['parse_source_row'] = !$sr->parsed;
        $returnArray['source_row'] = $sourceData;

        return $returnArray;
    }

    public function getNextData(): ?\Illuminate\Support\Collection
    {
        new Collection();
    }

    public function getSourceString(): string
    {
        return $this->source;
    }
}
