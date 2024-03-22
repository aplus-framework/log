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
use Framework\Log\Logger;
use Framework\Log\LogLevel;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Logger $logger;

    protected function logTests() : void
    {
        self::assertSame(
            'foo',
            $this->logger->getLastLog()->message
        );
        self::assertIsInt(
            $this->logger->getLastLog()->time
        );
    }

    public function testLog() : void
    {
        self::assertTrue($this->logger->log(LogLevel::DEBUG, 'foo'));
        self::assertSame(
            LogLevel::DEBUG,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testEmergency() : void
    {
        self::assertTrue($this->logger->logEmergency('foo'));
        self::assertSame(
            LogLevel::EMERGENCY,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testAlert() : void
    {
        self::assertTrue($this->logger->logAlert('foo'));
        self::assertSame(
            LogLevel::ALERT,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testCritical() : void
    {
        self::assertTrue($this->logger->logCritical('foo'));
        self::assertSame(
            LogLevel::CRITICAL,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testError() : void
    {
        self::assertTrue($this->logger->logError('foo'));
        self::assertSame(
            LogLevel::ERROR,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testWarning() : void
    {
        self::assertTrue($this->logger->logWarning('foo'));
        self::assertSame(
            LogLevel::WARNING,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testNotice() : void
    {
        self::assertTrue($this->logger->logNotice('foo'));
        self::assertSame(
            LogLevel::NOTICE,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testInfo() : void
    {
        self::assertTrue($this->logger->logInfo('foo'));
        self::assertSame(
            LogLevel::INFO,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testDebug() : void
    {
        self::assertTrue($this->logger->logDebug('foo'));
        self::assertSame(
            LogLevel::DEBUG,
            $this->logger->getLastLog()->level
        );
        $this->logTests();
    }

    public function testGetLastLog() : void
    {
        $this->logger->setLevel(LogLevel::INFO);
        self::assertNull($this->logger->getLastLog());
        self::assertTrue($this->logger->logDebug('foo'));
        self::assertNull($this->logger->getLastLog());
        self::assertTrue($this->logger->logInfo('foo'));
        self::assertInstanceOf(Log::class, $this->logger->getLastLog());
        $this->logger->setLevel(LogLevel::ERROR);
        self::assertTrue($this->logger->logInfo('foo'));
        self::assertNull($this->logger->getLastLog());
        self::assertTrue($this->logger->logEmergency('foo'));
        self::assertInstanceOf(Log::class, $this->logger->getLastLog());
    }

    public function testContext() : void
    {
        self::assertTrue($this->logger->logDebug('foo {a}bar', ['{a}' => 'x ']));
        self::assertSame(
            'foo x bar',
            $this->logger->getLastLog()->message
        );
    }

    public function testIdSize() : void
    {
        self::assertTrue($this->logger->logDebug('foo'));
        self::assertSame(
            12,
            \strlen($this->logger->getLastLog()->id)
        );
    }

    public function testLastLog() : void
    {
        $time = \time();
        self::assertTrue($this->logger->logCritical('foo bar'));
        $log = $this->logger->getLastLog();
        self::assertSame($time, $log->time);
        self::assertSame('foo bar', $log->message);
        self::assertSame(LogLevel::CRITICAL, $log->level);
    }

    public function testLastLogOnDisabledLevel() : void
    {
        $this->logger->setLevel(LogLevel::ERROR);
        $this->logger->logDebug('foo');
        self::assertNull($this->logger->getLastLog());
    }

    public function testLogLevel() : void
    {
        self::assertSame(LogLevel::DEBUG, $this->logger->getLevel());
        $this->logger->setLevel(LogLevel::INFO);
        self::assertSame(LogLevel::INFO, $this->logger->getLevel());
        $this->logger->setLevel(3);
        self::assertSame(LogLevel::WARNING, $this->logger->getLevel());
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage(
            '23 is not a valid backing value for enum Framework\Log\LogLevel'
        );
        $this->logger->setLevel(23);
    }

    public function testLogWithInteger() : void
    {
        self::assertTrue($this->logger->log(7, 'Foo bar'));
        self::assertSame(LogLevel::EMERGENCY, $this->logger->getLastLog()->level);
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage(
            '10 is not a valid backing value for enum Framework\Log\LogLevel'
        );
        $this->logger->log(10, 'Foo bar');
    }
}
