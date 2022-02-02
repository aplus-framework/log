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

use Framework\Log\Debug\LogCollector;
use Framework\Log\Logger;
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
        $directory = \sys_get_temp_dir() . '/logs';
        if ( ! \is_dir($directory)) {
            \mkdir($directory);
        }
        $logger = new Logger($directory);
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

    public function testDirectory() : void
    {
        $this->makeLogger();
        self::assertStringContainsString(
            \realpath(\sys_get_temp_dir() . '/logs') . \DIRECTORY_SEPARATOR,
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
        $logger->setLevel(Logger::CRITICAL);
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
        self::assertSame(
            [
                'collector',
                'class',
                'description',
                'start',
                'end',
            ],
            \array_keys($this->collector->getActivities()[0])
        );
    }
}
