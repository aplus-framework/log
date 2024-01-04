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

use Framework\Log\Loggers\FileLogger;
use Tests\Log\TestCase;

final class FileLoggerTest extends TestCase
{
    protected function setUp() : void
    {
        @\unlink(\sys_get_temp_dir() . '/tests.log');
        $this->logger = new FileLogger(destination: \sys_get_temp_dir() . '/tests.log');
    }

    public function testInvalidDestination() : void
    {
        $destination = \sys_get_temp_dir() . '/foo/bar/foo.log';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file destination: ' . $destination);
        new FileLogger(destination: $destination);
    }

    public function testWriteFailure() : void
    {
        if (\getenv('GITLAB_CI')) {
            $this->markTestIncomplete();
        }
        self::assertTrue($this->logger->logCritical('foo'));
        $destination = \sys_get_temp_dir() . '/tests.log';
        \chmod($destination, 0444);
        /*$this->expectError();
        $this->expectErrorMessage(
            'error_log(' . $destination . '): Failed to open stream: Permission denied'
        );*/
        self::assertFalse(@$this->logger->logCritical('foo'));
    }
}
