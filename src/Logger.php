<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Log;

use Framework\Log\Debug\LogCollector;
use JetBrains\PhpStorm\Pure;

/**
 * Class Logger.
 *
 * @see https://www.php-fig.org/psr/psr-3/
 *
 * @package log
 */
abstract class Logger
{
    /**
     * Logs destination.
     */
    protected string $destination;
    /**
     * @var array<mixed>
     */
    protected array $config;
    /**
     * Active log level.
     */
    protected LogLevel $level = LogLevel::DEBUG;
    protected Log | null $lastLog = null;
    protected LogCollector $debugCollector;

    /**
     * Logger constructor.
     *
     * @param string $destination
     * @param LogLevel $level
     * @param array<mixed> $config
     */
    public function __construct(
        string $destination,
        LogLevel $level = LogLevel::DEBUG,
        array $config = []
    ) {
        $this->setDestination($destination);
        $this->setLevel($level);
        $this->setConfig($config);
    }

    protected function setDestination(string $destination) : static
    {
        $this->destination = $destination;
        return $this;
    }

    public function getDestination() : string
    {
        return $this->destination;
    }

    /**
     * @param array<mixed> $config
     *
     * @return static
     */
    protected function setConfig(array $config) : static
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array<mixed>
     */
    protected function getConfig() : array
    {
        return $this->config;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param LogLevel $level
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function log(LogLevel $level, string $message, array $context = []) : bool
    {
        $debug = isset($this->debugCollector);
        if ($debug) {
            $start = \microtime(true);
        }
        $this->lastLog = null;
        if ($level->value < $this->getLevel()->value) {
            return true;
        }
        $time = \time();
        $id = $this->makeId();
        $message = $this->replaceContext($message, $context);
        $log = new Log($level, $message, $time, $id);
        $written = $this->write($log);
        if ($written) {
            $this->lastLog = $log;
        }
        if ($debug) {
            $end = \microtime(true);
            $this->debugCollector->addData([
                'start' => $start,
                'end' => $end,
                'date' => \date('Y-m-d', $time),
                'time' => \date('H:i:s', $time),
                'id' => $id,
                'level' => $level->value,
                'levelName' => $level->name,
                'message' => $message,
                'written' => $written,
            ]);
        }
        return $written;
    }

    protected function makeId() : string
    {
        return \bin2hex(\random_bytes(6));
    }

    /**
     * Get the last accepted log in the current instance.
     *
     * @return Log|null The last Log or null if the last was not accepted
     */
    #[Pure]
    public function getLastLog() : ?Log
    {
        return $this->lastLog;
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logDebug(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logInfo(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logNotice(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logWarning(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logError(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logCritical(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logAlert(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array<string> $context
     *
     * @return bool
     */
    public function logEmergency(string $message, array $context = []) : bool
    {
        return $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function getLevel() : LogLevel
    {
        return $this->level;
    }

    public function setLevel(LogLevel $level) : static
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @param string $message
     * @param array<string> $context
     *
     * @return string
     */
    #[Pure]
    protected function replaceContext(string $message, array $context) : string
    {
        return \strtr($message, $context);
    }

    public function setDebugCollector(LogCollector $debugCollector) : static
    {
        $this->debugCollector = $debugCollector;
        $this->debugCollector->setLogger($this);
        return $this;
    }

    abstract protected function write(Log $log) : bool;
}
