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

use Framework\Log\Loggers\SysLogger;
use Tests\Log\TestCase;

final class SysLoggerTest extends TestCase
{
    protected function setUp() : void
    {
        $this->logger = new SysLogger();
    }
}
