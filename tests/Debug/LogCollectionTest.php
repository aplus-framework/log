<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Log Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Log\Debug;

use Framework\Log\Debug\LogCollection;
use PHPUnit\Framework\TestCase;

final class LogCollectionTest extends TestCase
{
    protected LogCollection $collection;

    protected function setUp() : void
    {
        $this->collection = new LogCollection('Log');
    }

    public function testIcon() : void
    {
        self::assertStringStartsWith('<svg ', $this->collection->getIcon());
    }
}
