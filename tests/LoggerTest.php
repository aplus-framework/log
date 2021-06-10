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
		if (\is_dir($this->directory)) {
			\shell_exec('rm -rf ' . $this->directory);
		}
	}

	protected function getExpected(string $level)
	{
		return '#' . \date('H:i:s') . ' ' . $level . ' [a-z0-9]+' . ' foo' . \PHP_EOL . \PHP_EOL . '#';
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
		$this->assertMatchesRegularExpression(
			$this->getExpected('DEBUG'),
			$this->getContents()
		);
	}

	public function testEmergency()
	{
		$this->assertTrue($this->logger->emergency('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('EMERGENCY'),
			$this->getContents()
		);
	}

	public function testAlert()
	{
		$this->assertTrue($this->logger->alert('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('ALERT'),
			$this->getContents()
		);
	}

	public function testCritical()
	{
		$this->assertTrue($this->logger->critical('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('CRITICAL'),
			$this->getContents()
		);
	}

	public function testError()
	{
		$this->assertTrue($this->logger->error('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('ERROR'),
			$this->getContents()
		);
	}

	public function testWarning()
	{
		$this->assertTrue($this->logger->warning('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('WARNING'),
			$this->getContents()
		);
	}

	public function testNotice()
	{
		$this->assertTrue($this->logger->notice('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('NOTICE'),
			$this->getContents()
		);
	}

	public function testInfo()
	{
		$this->assertTrue($this->logger->info('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('INFO'),
			$this->getContents()
		);
	}

	public function testDebug()
	{
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertMatchesRegularExpression(
			$this->getExpected('DEBUG'),
			$this->getContents()
		);
	}

	public function testMultiLogs()
	{
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertTrue($this->logger->info('foo'));
		$this->assertTrue($this->logger->emergency('foo'));
		$time = \date('H:i:s');
		$this->assertMatchesRegularExpression(
			<<<EOL
			#{$time} DEBUG [a-z0-9]+ foo
			
			{$time} INFO [a-z0-9]+ foo
			
			{$time} EMERGENCY [a-z0-9]+ foo
			
			#
			EOL,
			$this->getContents()
		);
	}

	public function testMultiLogsOnDiabledLevel()
	{
		$this->logger = new LoggerMock($this->directory, $this->logger::INFO);
		$this->assertTrue($this->logger->debug('foo'));
		$this->assertTrue($this->logger->info('foo'));
		$this->assertTrue($this->logger->emergency('foo'));
		$time = \date('H:i:s');
		$this->assertMatchesRegularExpression(
			<<<EOL
			#{$time} INFO [a-z0-9]+ foo
			
			{$time} EMERGENCY [a-z0-9]+ foo
			
			#
			EOL,
			$this->getContents()
		);
	}

	public function testContext()
	{
		$this->assertTrue($this->logger->debug('foo {a}bar', ['{a}' => 'x ']));
		$this->assertMatchesRegularExpression(
			'#' . \date('H:i:s') . ' DEBUG [a-z0-9]+ foo x bar' . \PHP_EOL . \PHP_EOL . '#',
			$this->getContents()
		);
	}

	public function testLastLog()
	{
		$this->assertTrue($this->logger->critical('foo bar'));
		$log = $this->logger->getLastLog();
		$this->assertSame($this->directory . \date('Y-m-d') . '.log', $log->filename);
		$this->assertSame(\date('Y-m-d'), $log->date);
		$this->assertSame(\date('H:i:s'), $log->time);
		$this->assertSame('CRITICAL', $log->levelName);
		$this->assertSame('foo bar', $log->message);
		$this->assertTrue($log->written);
	}

	public function testLastLogOnDisabledLevel()
	{
		$this->logger = new LoggerMock($this->directory, $this->logger::INFO);
		$this->logger->debug('foo');
		$this->assertNull($this->logger->getLastLog());
	}

	public function testFlush()
	{
		\mkdir($this->directory . 'subdir');
		\touch($this->directory . '.hidden');
		\touch($this->directory . 'other.txt');
		$this->assertEquals(0, $this->logger->flush());
		$day = 60 * 60 * 24;
		\touch($this->directory . \date('Y-m-d') . '.log');
		\touch($this->directory . \date('Y-m-d', \time() - $day) . '.log');
		\touch($this->directory . \date('Y-m-d', \time() - 2 * $day) . '.log');
		$this->assertEquals(0, $this->logger->flush());
		$this->assertEquals(1, $this->logger->flush(\time() - $day));
		$this->assertEquals(2, $this->logger->flush(\time() + $day));
	}

	public function testFlushFailure()
	{
		\rmdir($this->directory);
		$this->assertFalse($this->logger->flush());
	}

	public function testWriteFailure()
	{
		$this->assertTrue($this->logger->critical('foo'));
		\chmod($this->directory . \date('Y-m-d') . '.log', 0444);
		$this->assertFalse($this->logger->critical('foo'));
	}

	public function testSanitizeMessage()
	{
		$this->assertSame('abc', $this->logger->sanitizeMessage(' abc  '));
		$this->assertSame(
			"1\n2\n3",
			$this->logger->sanitizeMessage("\n\t1\n 2\n\n 3 \t\n")
		);
	}
}
