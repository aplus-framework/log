<?php declare(strict_types=1);
/*
 * This file is part of The Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Log;

/**
 * Class Log.
 *
 * @property-read string $filename
 * @property-read string $date
 * @property-read string $time
 * @property-read string $levelName
 * @property-read string $id
 * @property-read string $message
 * @property-read bool   $written
 */
class Log
{
	protected string $filename;
	protected string $date;
	protected string $time;
	protected string $levelName;
	protected string $id;
	protected string $message;
	protected bool $written;

	public function __construct(string $filename, string $message, bool $written)
	{
		$this->filename = $filename;
		$this->date = \substr($filename, \strrpos($filename, \DIRECTORY_SEPARATOR) + 1, -4);
		[$this->time, $this->levelName, $this->id, $this->message] = \explode(' ', $message, 4);
		$this->written = $written;
	}

	public function __get(string $name)
	{
		if (\property_exists($this, $name)) {
			return $this->{$name};
		}
		throw new \Error(
			'Undefined property: ' . static::class . '::$' . $name
		);
	}

	public function __toString() : string
	{
		return \implode(' ', [$this->time, $this->levelName, $this->id, $this->message]);
	}
}
