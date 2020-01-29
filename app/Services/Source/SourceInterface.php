<?php


namespace App\Services\Source;


use Illuminate\Support\Collection;

interface SourceInterface
{
    public function getNextData(): ?array;

    public function getSourceString(): string;
}
