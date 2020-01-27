<?php


namespace App\Services\Matcher;


use App\Services\ParseServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface MatcherInterface
{
    public function match(array $row): ?Model;

    public function supports(ParseServiceInterface $parseService): bool;

    public function getMatchedBy(): string;

    public function getRate();
}
