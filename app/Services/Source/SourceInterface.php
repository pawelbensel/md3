<?php


namespace App\Services\Source;


use Illuminate\Support\Collection;

interface SourceInterface
{
    public function getNextData(): ?Collection;

    public function getSourceString(): string;
}
