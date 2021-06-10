<?php namespace Tests\Log;

use Framework\Log\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
	protected Log $log;

	protected function setUp() : void
	{
		$this->log = new Log(
			'/tmp/logs/2021-06-10.log',
			'01:55:15 DEBUG abc123 foo bar',
			true
		);
	}

	public function testProperties()
	{
		$this->assertSame('/tmp/logs/2021-06-10.log', $this->log->filename);
		$this->assertSame('2021-06-10', $this->log->date);
		$this->assertSame('01:55:15', $this->log->time);
		$this->assertSame('DEBUG', $this->log->levelName);
		$this->assertSame('abc123', $this->log->id);
		$this->assertSame('foo bar', $this->log->message);
		$this->assertTrue($this->log->written);
	}

	public function testInvalidProperty()
	{
		$this->expectException(\Error::class);
		$this->expectExceptionMessage(
			'Undefined property: Framework\Log\Log::$foo'
		);
		$this->log->foo;
	}

	public function testToString()
	{
		$this->assertSame(
			'01:55:15 DEBUG abc123 foo bar',
			(string) $this->log
		);
	}
}
