<?php
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log;

use Framework\Log\Log;
use Framework\Log\LogLevel;
use PHPUnit\Framework\TestCase;

final class LogTest extends TestCase
{
    protected Log $log;
    protected int $time;
    protected string $message;

    protected function setUp() : void
    {
        $this->time = \time();
        $this->log = new Log(
            LogLevel::INFO,
            <<<'EOL'
                foo
                bar baz

                bah tche

                EOL,
            $this->time,
            'abc123'
        );
    }

    public function testProperties() : void
    {
        self::assertSame(LogLevel::INFO, $this->log->level);
        self::assertSame(\time(), $this->log->time);
        self::assertSame('abc123', $this->log->id);
        self::assertSame(
            <<<'EOL'
                foo
                bar baz
                bah tche
                EOL,
            $this->log->message
        );
    }

    public function testToString() : void
    {
        self::assertSame(
            \date('Y-m-d H:i:s', $this->time) . ' INFO abc123 ' .
            <<<'EOL'
                foo
                bar baz
                bah tche
                EOL,
            (string) $this->log
        );
    }
}
