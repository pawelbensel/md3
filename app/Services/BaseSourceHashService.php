<?php 

namespace App\Services;


class BaseSourceHashService {	

	protected $source;

    public function __construct(?string $source)
    {
        $this->source = $source;        
    }

    /**
     * return hash from a database source hash table
     *
     * @var string
     * @var string
     */
    public function get($row,$type = null) {        
        $row = $this->makeRow($row);
        $hash = $this->makeHash($row);        
        if (!($sourceRow = $this->getByHash($hash,$type))) {
            return $this->create($row, $hash, $type);
        }
        return $sourceRow;
    }

     /**
     * Change row into json to save into DB
     *
     * @var string     
     */
    
    private function makeRow($row) {
        if (!is_string($row)) {
            if (is_array($row)||is_object($row)) {
                return json_encode($row,true);
            }
        } else 
                return (string) $row;
        return $row;
    }

    /**
     * Create hash base on source and row as Json
     *
     * @var string     
     */

    public function makeHash(string $row)
    {
        return md5($this->source.$row);
    }

}