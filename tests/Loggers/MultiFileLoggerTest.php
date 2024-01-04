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

use Framework\Log\Loggers\MultiFileLogger;
use Tests\Log\TestCase;

final class MultiFileLoggerTest extends TestCase
{
    protected function setUp() : void
    {
        $destination = \sys_get_temp_dir() . '/logs';
        if (!\is_dir($destination)) {
            \mkdir($destination);
        }
        \chmod($destination, 0777);
        $this->logger = new MultiFileLogger(destination: $destination);
    }

    public function testInvalidDestination() : void
    {
        $destination = \sys_get_temp_dir() . '/foo/bar';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid directory destination: ' . $destination);
        new MultiFileLogger(destination: $destination);
    }

    public function testWriteFailure() : void
    {
        if (\getenv('GITLAB_CI')) {
            $this->markTestIncomplete();
        }
        self::assertTrue($this->logger->logCritical('foo'));
        $dir = \sys_get_temp_dir() . '/logs';
        \chmod($dir, 0444);
        /*$destination = $dir . '/' . \date('Y-m-d') . '.log';
        $this->expectError();
        $this->expectErrorMessage(
            'error_log(' . $destination . '): Failed to open stream: Permission denied'
        );*/
        self::assertFalse(@$this->logger->logCritical('foo'));
    }
}
