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

use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    protected LoggerMock $logger;
    protected string $directory = '/tmp/logger/';

    public function setup() : void
    {
        if ( ! \is_dir($this->directory)) {
            \mkdir($this->directory);
        }
        $this->logger = new LoggerMock($this->directory);
    }

    protected function tearDown() : void
    {
        if (\is_dir($this->directory)) {
            \shell_exec('rm -rf ' . $this->directory);
        }
    }

    protected function getExpected(string $level) : string
    {
        return '#' . \date('H:i:s') . ' ' . $level . ' [a-z0-9]+' . ' foo' . \PHP_EOL . \PHP_EOL . '#';
    }

    protected function getContents() : string
    {
        // @phpstan-ignore-next-line
        return \file_get_contents($this->directory . \date('Y-m-d') . '.log');
    }

    public function testInvalidDirectory() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid directory path: ' . __FILE__
        );
        (new LoggerMock(__FILE__));
    }

    public function testInvalidLevel() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid level: ' . 10
        );
        (new LoggerMock($this->directory, 10));
    }

    public function testInvalidName() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid level: 8');
        $this->logger->getLevelName(8);
    }

    public function testLog() : void
    {
        self::assertTrue($this->logger->log($this->logger::DEBUG, 'foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('DEBUG'),
            $this->getContents()
        );
    }

    public function testEmergency() : void
    {
        self::assertTrue($this->logger->emergency('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('EMERGENCY'),
            $this->getContents()
        );
    }

    public function testAlert() : void
    {
        self::assertTrue($this->logger->alert('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('ALERT'),
            $this->getContents()
        );
    }

    public function testCritical() : void
    {
        self::assertTrue($this->logger->critical('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('CRITICAL'),
            $this->getContents()
        );
    }

    public function testError() : void
    {
        self::assertTrue($this->logger->error('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('ERROR'),
            $this->getContents()
        );
    }

    public function testWarning() : void
    {
        self::assertTrue($this->logger->warning('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('WARNING'),
            $this->getContents()
        );
    }

    public function testNotice() : void
    {
        self::assertTrue($this->logger->notice('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('NOTICE'),
            $this->getContents()
        );
    }

    public function testInfo() : void
    {
        self::assertTrue($this->logger->info('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('INFO'),
            $this->getContents()
        );
    }

    public function testDebug() : void
    {
        self::assertTrue($this->logger->debug('foo'));
        self::assertMatchesRegularExpression(
            $this->getExpected('DEBUG'),
            $this->getContents()
        );
    }

    public function testMultiLogs() : void
    {
        self::assertTrue($this->logger->debug('foo'));
        self::assertTrue($this->logger->info('foo'));
        self::assertTrue($this->logger->emergency('foo'));
        $time = \date('H:i:s');
        self::assertMatchesRegularExpression(
            <<<EOL
                #{$time} DEBUG [a-z0-9]+ foo

                {$time} INFO [a-z0-9]+ foo

                {$time} EMERGENCY [a-z0-9]+ foo

                #
                EOL,
            $this->getContents()
        );
    }

    public function testMultiLogsOnDisabledLevel() : void
    {
        $this->logger = new LoggerMock($this->directory, $this->logger::INFO);
        self::assertTrue($this->logger->debug('foo'));
        self::assertTrue($this->logger->info('foo'));
        self::assertTrue($this->logger->emergency('foo'));
        $time = \date('H:i:s');
        self::assertMatchesRegularExpression(
            <<<EOL
                #{$time} INFO [a-z0-9]+ foo

                {$time} EMERGENCY [a-z0-9]+ foo

                #
                EOL,
            $this->getContents()
        );
    }

    public function testContext() : void
    {
        self::assertTrue($this->logger->debug('foo {a}bar', ['{a}' => 'x ']));
        self::assertMatchesRegularExpression(
            '#' . \date('H:i:s') . ' DEBUG [a-z0-9]{12}+ foo x bar' . \PHP_EOL . \PHP_EOL . '#',
            $this->getContents()
        );
    }

    public function testIdSize() : void
    {
        self::assertTrue($this->logger->debug('foo'));
        $size = 12;
        self::assertMatchesRegularExpression(
            '#' . \date('H:i:s') . ' DEBUG [a-z0-9]{' . $size . '}+ foo' . \PHP_EOL . \PHP_EOL . '#',
            $this->getContents()
        );
    }

    public function testLastLog() : void
    {
        self::assertTrue($this->logger->critical('foo bar'));
        $log = $this->logger->getLastLog();
        self::assertSame($this->directory . \date('Y-m-d') . '.log', $log->filename);
        self::assertSame(\date('Y-m-d'), $log->date);
        self::assertSame(\date('H:i:s'), $log->time);
        self::assertSame('CRITICAL', $log->levelName);
        self::assertSame('foo bar', $log->message);
        self::assertTrue($log->written);
    }

    public function testLastLogOnDisabledLevel() : void
    {
        $this->logger = new LoggerMock($this->directory, $this->logger::INFO);
        $this->logger->debug('foo');
        self::assertNull($this->logger->getLastLog());
    }

    public function testGetLogs() : void
    {
        $date = \date('Y-m-d');
        $time = \date('H:i:s');
        self::assertSame([], $this->logger->getLogs($date));
        $this->logger->debug('debug ');
        $this->logger->critical(" critical\n\n\n\t oh, my... ");
        $logs = $this->logger->getLogs($date);
        self::assertCount(2, $logs);
        self::assertSame($this->directory . $date . '.log', $logs[0]->filename);
        self::assertSame($date, $logs[0]->date);
        self::assertSame($time, $logs[0]->time);
        self::assertSame('DEBUG', $logs[0]->levelName);
        self::assertSame('debug', $logs[0]->message);
        self::assertTrue($logs[0]->written);
        self::assertSame($this->directory . $date . '.log', $logs[1]->filename);
        self::assertSame($date, $logs[1]->date);
        self::assertSame($time, $logs[1]->time);
        self::assertSame('CRITICAL', $logs[1]->levelName);
        self::assertSame("critical\noh, my...", $logs[1]->message);
        self::assertTrue($logs[1]->written);
    }

    public function testGetLogsWithSlice() : void
    {
        $this->logger->critical('0');
        $this->logger->critical('1');
        $this->logger->critical('2');
        $this->logger->critical('3');
        $this->logger->critical('4');
        $this->logger->critical('5');
        $date = \date('Y-m-d');
        $logs = $this->logger->getLogs($date);
        self::assertCount(6, $logs);
        self::assertSame('0', $logs[0]->message);
        self::assertSame('5', $logs[\array_key_last($logs)]->message);
        $logs = $this->logger->getLogs($date, 2);
        self::assertCount(4, $logs);
        self::assertSame('2', $logs[0]->message);
        self::assertSame('5', $logs[\array_key_last($logs)]->message);
        $logs = $this->logger->getLogs($date, -2);
        self::assertCount(2, $logs);
        self::assertSame('4', $logs[0]->message);
        self::assertSame('5', $logs[\array_key_last($logs)]->message);
        $logs = $this->logger->getLogs($date, 2, 2);
        self::assertCount(2, $logs);
        self::assertSame('2', $logs[0]->message);
        self::assertSame('3', $logs[\array_key_last($logs)]->message);
        $logs = $this->logger->getLogs($date, 2, -3);
        self::assertCount(1, $logs);
        self::assertSame('2', $logs[0]->message);
        self::assertSame('2', $logs[\array_key_last($logs)]->message);
        $logs = $this->logger->getLogs($date, 4, 2);
        self::assertCount(2, $logs);
        self::assertSame('4', $logs[0]->message);
        self::assertSame('5', $logs[\array_key_last($logs)]->message);
    }

    public function testFlush() : void
    {
        \mkdir($this->directory . 'subdir');
        \touch($this->directory . '.hidden');
        \touch($this->directory . 'other.txt');
        self::assertSame(0, $this->logger->flush());
        $day = 60 * 60 * 24;
        \touch($this->directory . \date('Y-m-d') . '.log');
        \touch($this->directory . \date('Y-m-d', \time() - $day) . '.log');
        \touch($this->directory . \date('Y-m-d', \time() - 2 * $day) . '.log');
        self::assertSame(0, $this->logger->flush());
        self::assertSame(1, $this->logger->flush(\time() - $day));
        self::assertSame(2, $this->logger->flush(\time() + $day));
    }

    public function testFlushFailure() : void
    {
        \rmdir($this->directory);
        self::assertFalse($this->logger->flush());
    }

    public function testWriteFailure() : void
    {
        if (\getenv('GITLAB_CI')) {
            $this->markTestIncomplete();
        }
        self::assertTrue($this->logger->critical('foo'));
        \chmod($this->directory . \date('Y-m-d') . '.log', 0444);
        self::assertFalse($this->logger->critical('foo'));
    }

    public function testSanitizeMessage() : void
    {
        self::assertSame('abc', $this->logger->sanitizeMessage(' abc  '));
        self::assertSame(
            "1\n2\n3",
            $this->logger->sanitizeMessage("\n\t1\n 2\n\n 3 \t\n")
        );
    }
}
