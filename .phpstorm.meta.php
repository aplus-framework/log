<?php
/*
 * This file is part of The Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPSTORM_META;

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
