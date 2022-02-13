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
use InvalidArgumentException;

/**
 * Class MultiFileLogger.
 *
 * @package log
 */
class MultiFileLogger extends Logger
{
    protected function setDestination(string $destination) : static
    {
        $directory = \realpath($destination);
        if ( ! $directory || ! \is_dir($directory)) {
            throw new InvalidArgumentException('Invalid directory destination: ' . $destination);
        }
        $this->destination = $directory . \DIRECTORY_SEPARATOR;
        return $this;
    }

    protected function write(Log $log) : bool
    {
        $filename = $this->getDestination() . \date('Y-m-d', $log->time) . '.log';
        $eol = $this->getConfig()['eol'] ?? \PHP_EOL . \PHP_EOL;
        return \error_log($log . $eol, 3, $filename);
    }
}
