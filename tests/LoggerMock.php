<?php
/*
 * This file is part of The Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log;

use Framework\Log\Logger;

class LoggerMock extends Logger
{
    public function getLevelName(int $level) : string
    {
        return parent::getLevelName($level);
    }

    public function sanitizeMessage(string $message) : string
    {
        return parent::sanitizeMessage($message);
    }
}
