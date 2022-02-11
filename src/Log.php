<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Log Library.
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
 * @package log
 */
class Log implements \Stringable
{
    public readonly LogLevel $level;
    public readonly string $message;
    public readonly int $time;
    public readonly string $id;

    public function __construct(LogLevel $level, string $message, int $time, string $id)
    {
        $this->level = $level;
        $this->message = $this->sanitizeMessage($message);
        $this->time = $time;
        $this->id = $id;
    }

    public function __toString() : string
    {
        return \implode(' ', [
            \date('Y-m-d H:i:s', $this->time),
            $this->level->name,
            $this->id,
            $this->message,
        ]);
    }

    protected function sanitizeMessage(string $message) : string
    {
        $message = \explode(\PHP_EOL, $message);
        $lines = [];
        foreach ($message as $line) {
            $line = \trim($line);
            if ($line !== '') {
                $lines[] = $line;
            }
        }
        return \implode(\PHP_EOL, $lines);
    }
}
