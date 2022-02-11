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

use Framework\Log\LogLevel;
use PHPUnit\Framework\TestCase;

final class LogLevelTest extends TestCase
{
    public function testName() : void
    {
        foreach (LogLevel::cases() as $case) {
            self::assertSame(\strtoupper($case->name), $case->name);
        }
    }

    public function testLevels() : void
    {
        $current = -1;
        foreach (LogLevel::cases() as $case) {
            self::assertIsInt($case->value);
            self::assertGreaterThan($current, $case->value);
            $current = $case->value;
        }
    }
}
