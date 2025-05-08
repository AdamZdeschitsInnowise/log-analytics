<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\LogParser\Parser;

use App\Service\LogParser\Parser\Entry;
use App\Service\LogParser\Parser\ParserException;
use App\Service\LogParser\Parser\RegexStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegexStrategy::class)]
class RegexStrategyTest extends TestCase
{
    public function testParsePositive(): void
    {
        $bom = pack('H*', 'EFBBBF');

        $line = $bom.'USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201';

        $parser = new RegexStrategy();

        $this->assertEquals(
            new Entry(
                'USER-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:21:56'),
                'POST /users HTTP/1.1',
                201
            ),
            $parser->parse($line)
        );
    }

    #[TestWith(['VERY BROKEN STRING', '/does not match regular expression/'])]
    #[TestWith(['USER-SERVICE - - [99/Bob/0018:09:21:56 +0000] "POST /users HTTP/1.1" 201', '/not a valid date format/'])]
    public function testParseNegativeLine(string $line, string $message): void
    {
        $parser = new RegexStrategy();

        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches($message);

        $parser->parse($line);
    }
}
