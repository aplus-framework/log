<?php namespace Framework\Log;

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
	 *
	 * @var string
	 */
	protected $directory;
	/**
	 * Active log level.
	 *
	 * @var int
	 */
	protected $level = 2;

	/**
	 * Logger constructor.
	 *
	 * @param string $directory
	 * @param int    $level
	 *
	 * @throws \InvalidArgumentException if directory is invalid,
	 *                                   if log level is invalid
	 */
	public function __construct(string $directory, int $level = 0)
	{
		$directory = \realpath($directory);
		if ( ! $directory || ! \is_dir($directory)) {
			throw new \InvalidArgumentException('Invalid directory path: ' . $directory);
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
	 * @throws \InvalidArgumentException if log level is invalid
	 *
	 * @return bool
	 */
	public function log(int $level, string $message, array $context = []) : bool
	{
		$this->validateLevel($level);
		if ($level < $this->level) {
			return true;
		}
		$message = $this->replaceContext($message, $context);
		$message = \date('H:i:s') . ' ' . $this->getLevelName($level) . ' '
			. $this->sanitizeMessage($message) . ' ' . \PHP_EOL;
		return $this->write($message);
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
			throw new \InvalidArgumentException('Invalid level: ' . $level);
		}
	}

	protected function replaceContext(string $message, array $context) : string
	{
		$replace = [];
		foreach ($context as $key => $value) {
			$replace['{' . $key . '}'] = $value;
		}
		return \strtr($message, $replace);
	}

	protected function getLevelName(int $level) : string
	{
		if ($level === 7) {
			return 'EMERGENCY';
		}
		if ($level === 6) {
			return 'ALERT';
		}
		if ($level === 5) {
			return 'CRITICAL';
		}
		if ($level === 4) {
			return 'ERROR';
		}
		if ($level === 3) {
			return 'WARNING';
		}
		if ($level === 2) {
			return 'NOTICE';
		}
		if ($level === 1) {
			return 'INFO';
		}
		if ($level === 0) {
			return 'DEBUG';
		}
		throw new \InvalidArgumentException('Invalid level: ' . $level);
	}

	protected function sanitizeMessage(string $message) : string
	{
		$message = \trim($message);
		$parts = \explode(\PHP_EOL, $message);
		foreach ($parts as &$part) {
			$part = \trim($part);
		}
		unset($part);
		$message = \implode(\PHP_EOL, $parts);
		return $message;
	}

	protected function write(string $message) : bool
	{
		$file = $this->directory . \date('Y-m-d') . '.log';
		$handle = \fopen($file, 'ab');
		$write = \fwrite($handle, $message);
		\fclose($handle);
		return $write !== false;
	}
}
