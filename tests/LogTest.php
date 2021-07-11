<?php
/*
 * This file is part of The Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log;

use Framework\Log\Log;
use PHPUnit\Framework\TestCase;

final class LogTest extends TestCase
{
    protected Log $log;

    protected function setUp() : void
    {
        $this->log = new Log(
            '/tmp/logs/2021-06-10.log',
            '01:55:15 DEBUG abc123 foo bar',
            true
        );
    }

    public function testProperties() : void
    {
        self::assertSame('/tmp/logs/2021-06-10.log', $this->log->filename);
        self::assertSame('2021-06-10', $this->log->date);
        self::assertSame('01:55:15', $this->log->time);
        self::assertSame('DEBUG', $this->log->levelName);
        self::assertSame('abc123', $this->log->id);
        self::assertSame('foo bar', $this->log->message);
        self::assertTrue($this->log->written);
    }

    public function testInvalidProperty() : void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Undefined property: Framework\Log\Log::$foo'
        );
        $this->log->foo; // @phpstan-ignore-line
    }

    public function testToString() : void
    {
        self::assertSame(
            '01:55:15 DEBUG abc123 foo bar',
            (string) $this->log
        );
    }
}
