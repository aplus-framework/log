<?php namespace Tests\Log;

use Framework\Log\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
	/**
	 * @var Logger
	 */
	protected $logger;
	protected $directory = '/tmp/logger';

	public function setup() : void
	{
		if ( ! \is_dir($this->directory)) {
			\mkdir($this->directory);
		}
		$this->logger = new Logger($this->directory, 0);
	}

	protected function tearDown() : void
	{
		\shell_exec('rm -r ' . $this->directory);
	}

	public function testLog()
	{
		$this->assertTrue($this->logger->log($this->logger::DEBUG, 'Debug'));
	}
}
