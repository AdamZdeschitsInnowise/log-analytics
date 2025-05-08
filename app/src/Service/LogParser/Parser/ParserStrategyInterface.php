<?php

declare(strict_types=1);

namespace App\Service\LogParser\Parser;

interface ParserStrategyInterface
{
    /**
     * @throws ParserException
     */
    public function parse(string $line): Entry;
}
