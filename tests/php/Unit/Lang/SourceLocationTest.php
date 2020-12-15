<?php

declare(strict_types=1);

namespace PhelTest\Unit\Lang;

use Phel\Lang\SourceLocation;
use PHPUnit\Framework\TestCase;

final class SourceLocationTest extends TestCase
{
    public function testGetFile()
    {
        $s = new SourceLocation('/test', 1, 2);
        $this->assertEquals('/test', $s->getFile());
    }

    public function testSetFile()
    {
        $s = new SourceLocation('/test', 1, 2);
        $s->setFile('/abc');
        $this->assertEquals('/abc', $s->getFile());
    }

    public function testGetLine()
    {
        $s = new SourceLocation('/test', 1, 2);
        $this->assertEquals(1, $s->getLine());
    }

    public function testSetLine()
    {
        $s = new SourceLocation('/test', 1, 2);
        $s->setLine(32);
        $this->assertEquals(32, $s->getLine());
    }

    public function testGetColumn()
    {
        $s = new SourceLocation('/test', 1, 2);
        $this->assertEquals(2, $s->getColumn());
    }

    public function testSetColumn()
    {
        $s = new SourceLocation('/test', 1, 2);
        $s->setColumn(32);
        $this->assertEquals(32, $s->getColumn());
    }
}
