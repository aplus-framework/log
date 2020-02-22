<?php namespace Tests\Log;

use Framework\Log\Logger;

class LoggerMock extends Logger
{
	public function getLevelName(int $level) : string
	{
		return parent::getLevelName($level);
	}
}
