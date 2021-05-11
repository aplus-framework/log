<?php namespace PHPSTORM_META;

registerArgumentsSet(
	'log_level',
	\Framework\Log\Logger::DEBUG,
	\Framework\Log\Logger::INFO,
	\Framework\Log\Logger::NOTICE,
	\Framework\Log\Logger::WARNING,
	\Framework\Log\Logger::ERROR,
	\Framework\Log\Logger::CRITICAL,
	\Framework\Log\Logger::ALERT,
	\Framework\Log\Logger::EMERGENCY,
);
expectedArguments(
	\Framework\Log\Logger::__construct(),
	1,
	argumentsSet('log_level')
);
expectedArguments(
	\Framework\Log\Logger::log(),
	0,
	argumentsSet('log_level')
);
