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
 * Class EmailLogger.
 *
 * @package log
 */
class EmailLogger extends Logger
{
    protected function setDestination(string $destination) : static
    {
        if ( ! \filter_var($destination, \FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email destination: ' . $destination);
        }
        $this->destination = $destination;
        return $this;
    }

    protected function makeHeaders(Log $log) : string
    {
        $headers = $this->getConfig()['headers'] ?? [];
        $names = [];
        foreach ($headers as $name => $value) {
            $names[] = \strtolower($name);
        }
        if ( ! \in_array('subject', $names, true)) {
            $headers['Subject'] = 'Log ' . $log->level->name . ' ' . $log->id;
        }
        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }
        return \implode("\r\n", $headerLines);
    }

    protected function write(Log $log) : bool
    {
        return \error_log(
            (string) $log,
            1,
            $this->getDestination(),
            $this->makeHeaders($log)
        );
    }
}
