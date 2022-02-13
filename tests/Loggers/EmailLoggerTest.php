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

use Framework\Log\Loggers\EmailLogger;
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
}
