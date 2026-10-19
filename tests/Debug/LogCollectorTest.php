<?php
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log\Debug;

use Aplus\Debug\Activity;
use Aplus\Log\Debug\LogCollector;
use Aplus\Log\Logger;
use Aplus\Log\Loggers\FileLogger;
use Aplus\Log\LogLevel;
use PHPUnit\Framework\TestCase;

final class LogCollectorTest extends TestCase
{
    protected LogCollector $collector;

    protected function setUp() : void
    {
        $this->collector = new LogCollector();
    }

    protected function makeLogger() : Logger
    {
        $destination = \sys_get_temp_dir() . '/tests.log';
        if (!\is_file($destination)) {
            \touch($destination);
        }
        \chmod($destination, 755);
        $logger = new FileLogger(destination: $destination);
        $logger->setDebugCollector($this->collector);
        return $logger;
    }

    public function testNoLogger() : void
    {
        self::assertStringContainsString(
            'A Logger instance has not been set',
            $this->collector->getContents()
        );
    }

    public function testDestination() : void
    {
        $this->makeLogger();
        self::assertStringContainsString(
            \sys_get_temp_dir() . '/tests.log',
            $this->collector->getContents()
        );
    }

    public function testCurrentLevel() : void
    {
        $logger = $this->makeLogger();
        self::assertStringContainsString(
            '0 DEBUG',
            $this->collector->getContents()
        );
        $logger->setLevel(LogLevel::CRITICAL);
        self::assertStringContainsString(
            '5 CRITICAL',
            $this->collector->getContents()
        );
    }

    public function testNoLogs() : void
    {
        $this->makeLogger();
        self::assertStringContainsString(
            'No log has been set',
            $this->collector->getContents()
        );
    }

    public function testLogs() : void
    {
        $logger = $this->makeLogger();
        $logger->logAlert('Foo Alert');
        $contents = $this->collector->getContents();
        self::assertStringContainsString(
            '1 log has been set',
            $contents
        );
        self::assertStringContainsString(
            'Foo Alert',
            $contents
        );
        $logger->logCritical('Bar Critical');
        $contents = $this->collector->getContents();
        self::assertStringContainsString(
            '2 logs have been set',
            $contents
        );
        self::assertStringContainsString(
            'Foo Alert',
            $contents
        );
        self::assertStringContainsString(
            'Bar Critical',
            $contents
        );
    }

    public function testActivities() : void
    {
        $this->makeLogger()->logInfo('foo');
        self::assertInstanceOf(
            Activity::class,
            $this->collector->getActivities()[0]
        );
    }
}
