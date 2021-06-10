<?php namespace Framework\Log;

use InvalidArgumentException;

/**
 * Class Logger.
 *
 * @see https://www.php-fig.org/psr/psr-3/
 */
class Logger
{
	/**
	 * Emergency level.
	 */
	public const EMERGENCY = 7;
	/**
	 * Alert level.
	 */
	public const ALERT = 6;
	/**
	 * Critical level.
	 */
	public const CRITICAL = 5;
	/**
	 * Error level.
	 */
	public const ERROR = 4;
	/**
	 * Warning level.
	 */
	public const WARNING = 3;
	/**
	 * Notice level.
	 */
	public const NOTICE = 2;
	/**
	 * Info level.
	 */
	public const INFO = 1;
	/**
	 * Debug level.
	 */
	public const DEBUG = 0;
	/**
	 * Logs directory path.
	 */
	protected string $directory;
	/**
	 * Active log level.
	 */
	protected int $level = Logger::NOTICE;
	protected Log | null $lastLog = null;

	/**
	 * Logger constructor.
	 *
	 * @param string $directory
	 * @param int    $level
	 *
	 * @throws InvalidArgumentException if directory is invalid,
	 *                                  if log level is invalid
	 */
	public function __construct(string $directory, int $level = self::DEBUG)
	{
		$directory = \realpath($directory);
		if ( ! $directory || ! \is_dir($directory)) {
			throw new InvalidArgumentException('Invalid directory path: ' . $directory);
		}
		$this->validateLevel($level);
		$this->directory = $directory . \DIRECTORY_SEPARATOR;
		$this->level = $level;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param int    $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @throws InvalidArgumentException if log level is invalid
	 *
	 * @return bool
	 */
	public function log(int $level, string $message, array $context = []) : bool
	{
		$this->validateLevel($level);
		$this->lastLog = null;
		if ($level < $this->level) {
			return true;
		}
		$time = \date('H:i:s');
		$level = $this->getLevelName($level);
		$id = \bin2hex(\random_bytes(10));
		$message = $this->replaceContext($message, $context);
		$message = $this->sanitizeMessage($message);
		$message = $time . ' ' . $level . ' ' . $id . ' ' . $message;
		return $this->write($message);
	}

	/**
	 * Get the last written log.
	 *
	 * @return Log|null
	 */
	public function getLastLog() : ?Log
	{
		return $this->lastLog;
	}

	/**
	 * Get logs by date.
	 *
	 * @param string $date The date in the format `Y-m-d`
	 *
	 * @return array|Log[]
	 */
	public function getLogs(string $date) : array
	{
		$file = $this->directory . $date . '.log';
		if ( ! \is_file($file)) {
			return [];
		}
		$contents = (string) \file_get_contents($file);
		$contents = \explode(\PHP_EOL . \PHP_EOL, $contents);
		$logs = [];
		foreach ($contents as $log) {
			if ($log !== '') {
				$logs[] = new Log($file, $log, true);
			}
		}
		return $logs;
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function debug(string $message, array $context = []) : bool
	{
		return $this->log(static::DEBUG, $message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function info(string $message, array $context = []) : bool
	{
		return $this->log(static::INFO, $message, $context);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function notice(string $message, array $context = []) : bool
	{
		return $this->log(static::NOTICE, $message, $context);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function warning(string $message, array $context = []) : bool
	{
		return $this->log(static::WARNING, $message, $context);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function error(string $message, array $context = []) : bool
	{
		return $this->log(static::ERROR, $message, $context);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function critical(string $message, array $context = []) : bool
	{
		return $this->log(static::CRITICAL, $message, $context);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function alert(string $message, array $context = []) : bool
	{
		return $this->log(static::ALERT, $message, $context);
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 */
	public function emergency(string $message, array $context = []) : bool
	{
		return $this->log(static::EMERGENCY, $message, $context);
	}

	protected function validateLevel(int $level) : void
	{
		if ( ! \in_array($level, [
			static::EMERGENCY,
			static::ALERT,
			static::CRITICAL,
			static::ERROR,
			static::WARNING,
			static::NOTICE,
			static::INFO,
			static::DEBUG,
		], true)) {
			throw new InvalidArgumentException('Invalid level: ' . $level);
		}
	}

	protected function replaceContext(string $message, array $context) : string
	{
		return \strtr($message, $context);
	}

	protected function getLevelName(int $level) : string
	{
		switch ($level) {
			case static::DEBUG:
				return 'DEBUG';
			case static::INFO:
				return 'INFO';
			case static::NOTICE:
				return 'NOTICE';
			case static::WARNING:
				return 'WARNING';
			case static::ERROR:
				return 'ERROR';
			case static::CRITICAL:
				return 'CRITICAL';
			case static::ALERT:
				return 'ALERT';
			case static::EMERGENCY:
				return 'EMERGENCY';
		}
		throw new InvalidArgumentException('Invalid level: ' . $level);
	}

	protected function sanitizeMessage(string $message) : string
	{
		$message = \explode(\PHP_EOL, $message);
		$lines = [];
		foreach ($message as $line) {
			$line = \trim($line);
			if ($line !== '') {
				$lines[] = $line;
			}
		}
		return \implode(\PHP_EOL, $lines);
	}

	protected function write(string $message) : bool
	{
		$date = \date('Y-m-d');
		$file = $this->directory . $date . '.log';
		$is_file = \is_file($file);
		$handle = @\fopen($file, 'ab');
		if ($handle === false) {
			$this->lastLog = new Log($file, $message, false);
			return false;
		}
		\flock($handle, \LOCK_EX);
		$written = \fwrite($handle, $message . \PHP_EOL . \PHP_EOL);
		\flock($handle, \LOCK_UN);
		\fclose($handle);
		if ($is_file === false) {
			\chmod($file, 0644);
		}
		$written = $written !== false;
		$this->lastLog = new Log($file, $message, $written);
		return $written;
	}

	/**
	 * Flush log files.
	 *
	 * @param int|null $before Flush files before timestamp
	 *
	 * @return false|int The number of deleted files or false on failure
	 */
	public function flush(int $before = null) : int | false
	{
		if ($before !== null) {
			$before = \date('Y-m-d', $before);
		}
		$handle = @\opendir($this->directory);
		if ($handle === false) {
			return false;
		}
		$deleted_count = 0;
		while (($path = \readdir($handle)) !== false) {
			$filename = $this->directory . $path;
			if ($path[0] === '.' || ! \is_file($filename)) {
				continue;
			}
			if ($path < $before
				&& \str_ends_with($path, '.log')
				&& \unlink($filename)
			) {
				++$deleted_count;
			}
		}
		return $deleted_count;
	}
}
