<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Log\Loggers;

use Framework\Log\Log;
use Framework\Log\Logger;
use Framework\Log\LogLevel;

/**
 * Class SysLogger.
 *
 * @package log
 */
class SysLogger extends Logger
{
    public function __construct(
        string $destination = '',
        LogLevel $level = LogLevel::DEBUG,
        array $config = []
    ) {
        parent::__construct($destination, $level, $config);
    }

    protected function write(Log $log) : bool
    {
        return \error_log($log . ' ', 0);
    }
}
