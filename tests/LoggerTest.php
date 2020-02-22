<?php namespace Tests\Log;

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
	protected LoggerMock $logger;
	protected string $directory = '/tmp/logger/';

	public function setup() : void
	{
		if ( ! \is_dir($this->directory)) {
			\mkdir($this->directory);
		}
		$this->logger = new LoggerMock($this->directory);
	}

	protected function tearDown() : void
	{
		\shell_exec('rm -r ' . $this->directory);
	}

	protected function getExpected(string $level)
	{
		return \date('H:i:s') . ' ' . $level . ' foo ' . \PHP_EOL;
	}

	protected function getContents() : string
	{
		return \file_get_contents($this->directory . \date('Y-m-d') . '.log');
	}

	public function testInvalidDirectory()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Invalid directory path: ' . __FILE__
		);
		(new LoggerMock(__FILE__));
	}

	public function testInvalidLevel()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage(
			'Invalid level: ' . 10
		);
		(new LoggerMock($this->directory, 10));
	}

	public function testInvalidName()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid level: 8');
		$this->logger->getLevelName(8);
	}

	public function testLog()
	{
		$this->assertTrue($this->logger->log($this->logger::DEBUG, 'foo'));
		$this->assertEquals(
			$this->getExpected('DEBUG'),
			$this->getContents()
		);
	}

	public function testEmergency()
	{
		$this->assertTrue($this->logger->emergency('foo'));
		$this->assertEquals(
			$this->getExpected('EMERGENCY'),
			$this->getContents()
		);
	}

	public function testAlert()
	{
		$this->assertTrue($this->logger->alert('foo'));
		$this->assertEquals(
			$this->getExpected('ALERT'),
			$this->getContents()
		);
	}

	public function testCritical()
	{
		$this->assertTrue($this->logger->critical('foo'));
		$this->assertEquals(
			$this->getExpected('CRITICAL'),
			$this->getContents()
		);
	}

	public function testError()
	{
		$this->assertTrue($this->logger->error('foo'));
		$this->assertEquals(
			$this->getExpected('ERROR'),
			$this->getContents()
		);
	}

	public function testWarning()
	{
		$this->assertTrue($this->logger->warning('foo'));
		$this->assertEquals(
			$this->getExpected('WARNING'),
			$this->getContents()
		);
	}

	public function testNotice()
	{
		$this->assertTrue($this->logger->notice('foo'));
		$this->assertEquals(
			$this->getExpected('NOTICE'),
			$this->getContents()
		);
	}

	public function testInfo()
	{
		$this->assertTrue($this->logger->info('foo'));
		$this->assertEquals(
			$this->getExpected('INFO'),
			$this->getContents()
		);
	}

	public function testDebug()
	{
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertEquals(
			$this->getExpected('DEBUG'),
			$this->getContents()
		);
	}

	public function testMultiLogs()
	{
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertTrue($this->logger->info('foo'));
		$this->assertTrue($this->logger->emergency('foo'));
		$this->assertEquals(
			$this->getExpected('DEBUG')
			. $this->getExpected('INFO')
			. $this->getExpected('EMERGENCY'),
			$this->getContents()
		);
	}

	public function testMultiLogsOnDiabledLevel()
	{
		$this->logger = new LoggerMock($this->directory, $this->logger::INFO);
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertTrue($this->logger->info('foo'));
		$this->assertTrue($this->logger->emergency('foo'));
		$this->assertEquals(
			$this->getExpected('INFO')
			. $this->getExpected('EMERGENCY'),
			$this->getContents()
		);
	}

	public function testContext()
	{
		$this->assertTrue($this->logger->debug('foo {a}bar', ['a' => 'x ']));
		$this->assertEquals(
			\date('H:i:s') . ' DEBUG foo x bar ' . \PHP_EOL,
			$this->getContents()
		);
	}
}
