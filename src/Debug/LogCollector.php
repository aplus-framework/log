<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Log\Debug;

use Framework\Debug\Collector;
use Framework\Log\Logger;

/**
 * Class LogCollector.
 *
 * @package log
 */
class LogCollector extends Collector
{
    protected Logger $logger;

    public function setLogger(Logger $logger) : static
    {
        $this->logger = $logger;
        return $this;
    }

    public function getActivities() : array
    {
        $activities = [];
        foreach ($this->getData() as $index => $data) {
            $activities[] = [
                'collector' => $this->getName(),
                'class' => static::class,
                'description' => 'Set log ' . $index + 1,
                'start' => $data['start'],
                'end' => $data['end'],
            ];
        }
        return $activities;
    }

    public function getContents() : string
    {
        if ( ! isset($this->logger)) {
            return '<p>A Logger instance has not been set on this collector.</p>';
        }
        \ob_start(); ?>
        <p><strong>Directory:</strong> <?= \htmlentities($this->logger->getDirectory()) ?></p>
        <p><strong>Log Level:</strong> <?= \htmlentities(
            $this->logger->getLevel() . ' ' .
                $this->logger->getLevelName($this->logger->getLevel())
        ) ?>
        </p>
        <h1>Logs</h1>
        <?= $this->renderLogs() ?>
        <h1>Available Levels</h1>
        <table>
            <thead>
            <tr>
                <th>Level</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->logger->getLevels() as $level): ?>
                <tr<?= $level === $this->logger->getLevel()
                    ? ' class="active" title="Current level"'
                    : '' ?>>
                    <td><?= \htmlentities((string) $level) ?></td>
                    <td><?= \htmlentities($this->logger->getLevelName($level)) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php
        return \ob_get_clean(); // @phpstan-ignore-line
    }

    protected function renderLogs() : string
    {
        if ( ! $this->hasData()) {
            return '<p>No log has been set.</p>';
        }
        $count = \count($this->getData());
        \ob_start(); ?>
        <p><?= $count ?> log<?= $count === 1 ? ' has' : 's have' ?> been set.</p>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Time</th>
                <th>Id</th>
                <th colspan="2">Level</th>
                <th>Message</th>
                <th>Written</th>
                <th title="Seconds">Time to Log</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->getData() as $index => $data): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= \htmlentities($data['date']) ?></td>
                    <td><?= \htmlentities($data['time']) ?></td>
                    <td><?= \htmlentities($data['id']) ?></td>
                    <td><?= \htmlentities((string) $data['level']) ?></td>
                    <td><?= \htmlentities($data['levelName']) ?></td>
                    <td>
                        <pre><code class="language-log"><?= \htmlentities($data['message']) ?></code></pre>
                    </td>
                    <td><?= $data['written'] ? 'Yes' : 'No' ?></td>
                    <td><?= \round($data['end'] - $data['start'], 6) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php
        return \ob_get_clean(); // @phpstan-ignore-line
    }
}
