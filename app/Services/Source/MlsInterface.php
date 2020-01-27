<?php


namespace App\Services\Source;


interface MlsInterface
{
    public function setMlsName(string $mls_name);

    public function getMlsName():string;
}
