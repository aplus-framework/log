<?php
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log\Loggers;

use Framework\Log\Log;
use Framework\Log\Loggers\EmailLogger;
use Framework\Log\LogLevel;
use Tests\Log\TestCase;

final class EmailLoggerTest extends TestCase
{
    protected function setUp() : void
    {
        $this->logger = new EmailLogger(destination: 'developer@localhost.localdomain');
    }

    public function testInvalidDestination() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email destination: foo.tld');
        new EmailLogger(destination: 'foo.tld');
    }

    public function testMakeHeaders() : void
    {
        $logger = new class('developer@localhost.localdomain') extends EmailLogger {
            public function setConfig(array $config) : static
            {
                return parent::setConfig($config);
            }

            public function makeHeaders(Log $log) : string
            {
                return parent::makeHeaders($log);
            }
        };
        $log = new Log(LogLevel::DEBUG, 'Foo', \time(), 'abc');
        self::assertSame('Subject: Log DEBUG abc', $logger->makeHeaders($log));
        $logger->setConfig([
            'headers' => [
                'subject' => 'Foo bar',
                'Foo' => 'Bar',
            ],
        ]);
        self::assertSame(
            "subject: Foo bar\r\nFoo: Bar",
            $logger->makeHeaders($log)
        );
    }
}
