<?php

declare(strict_types=1);

namespace App\Service\LogParser\Parser;

final readonly class RegexStrategy implements ParserStrategyInterface
{
    private const string LOG_PATTERN = '/^\xEF?\xBB?\xBF?([A-Za-z-]+?) - - \[([^\]]+?)\] "(.*?)" (\d+)$/';
    private const string DATETIME_FORMAT = 'd/M/Y:H:i:s O';

    public function parse(string $line): Entry
    {
        if (0 === preg_match(self::LOG_PATTERN, trim($line), $matches)) {
            throw new ParserException(sprintf(
                'Line "%s" does not match regular expression "%s"',
                $line,
                self::LOG_PATTERN
            ));
        }

        $date = \DateTime::createFromFormat(self::DATETIME_FORMAT, $matches[2]);

        if (false === $date) {
            throw new ParserException(sprintf(
                'Date "%s" is not a valid date format %s',
                $matches[2],
                self::DATETIME_FORMAT
            ));
        }

        return new Entry(
            serviceName: $matches[1],
            timestamp: $date,
            requestData: $matches[3],
            statusCode: (int) $matches[4],
        );
    }
}
