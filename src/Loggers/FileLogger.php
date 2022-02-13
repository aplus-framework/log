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
 * Class FileLogger.
 *
 * @package log
 */
class FileLogger extends Logger
{
    protected function setDestination(string $destination) : static
    {
        if ( ! \is_file($destination) && ! \is_dir(\dirname($destination))) {
            throw new InvalidArgumentException('Invalid file destination: ' . $destination);
        }
        $this->destination = $destination;
        return $this;
    }

    protected function write(Log $log) : bool
    {
        $eol = $this->getConfig()['eol'] ?? \PHP_EOL . \PHP_EOL;
        return \error_log($log . $eol, 3, $this->getDestination());
    }
}
