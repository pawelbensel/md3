<?php


namespace App\Services\Report\Interfaces;


interface MultiMlsableSql
{
    public function replaceMls(string $newOrgId);
}
