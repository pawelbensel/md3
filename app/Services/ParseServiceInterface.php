<?php


namespace App\Services;


interface ParseServiceInterface
{
    public function getId($row);
    public function setSourceRowId($sourceRowId);
}
