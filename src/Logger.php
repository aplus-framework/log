<?php namespace Framework\Log;

/**
 * Class Logger.
 *
 * @see https://www.php-fig.org/psr/psr-3/
 */
class Logger
{
	public const EMERGENCY = 7;
	public const ALERT = 6;
	public const CRITICAL = 5;
	public const ERROR = 4;
	public const WARNING = 3;
	public const NOTICE = 2;
	public const INFO = 1;
	public const DEBUG = 0;
	/**
	 * @var string
	 */
	protected $directory;
	/**
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
	public function __construct(string $directory, int $level = 2)
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
		$message = \date('H:i:s') . ' ' . $level . ' ' . \trim($message) . ' ';
		return $this->write($message);
	}

	public function debug(string $message, array $context = []) : bool
	{
		return $this->log(static::DEBUG, $message, $context);
	}

	public function info(string $message, array $context = []) : bool
	{
		return $this->log(static::INFO, $message, $context);
	}

	public function notice(string $message, array $context = []) : bool
	{
		return $this->log(static::NOTICE, $message, $context);
	}

	public function warning(string $message, array $context = []) : bool
	{
		return $this->log(static::WARNING, $message, $context);
	}

	public function error(string $message, array $context = []) : bool
	{
		return $this->log(static::ERROR, $message, $context);
	}

	public function critical(string $message, array $context = []) : bool
	{
		return $this->log(static::CRITICAL, $message, $context);
	}

	public function alert(string $message, array $context = []) : bool
	{
		return $this->log(static::ALERT, $message, $context);
	}

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

	protected function write(string $message) : bool
	{
		$file = $this->directory . \date('Y-m-d') . '.log';
		$handle = \fopen($file, 'ab');
		$write = \fwrite($handle, $message);
		\fclose($handle);
		return $write !== false;
	}
}
