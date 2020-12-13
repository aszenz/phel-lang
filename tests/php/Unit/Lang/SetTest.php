<?php

declare(strict_types=1);

namespace PhelTest\Unit\Lang;

use Phel\Lang\PhelArray;
use Phel\Lang\Set;
use PHPUnit\Framework\TestCase;

final class SetTest extends TestCase
{
    public function testCount() {
        $set1 = new Set([]);
        $set2 = new Set(["a"]);
        $set3 = new Set(["a", "b"]);
        $set4 = new Set(["a", "b", "b"]);
        $this->assertEquals(0, count($set1));
        $this->assertEquals(1, count($set2));
        $this->assertEquals(2, count($set3));
        $this->assertEquals(2, count($set4));
    }

    public function testHash() {
        $set = new Set(["a"]);
        $this->assertEquals(spl_object_hash($set), $set->hash());
    }

    public function testForeach() {
        $set = new Set(["a", "b"]);
        $result = [];
        foreach ($set as $k => $entry) {
            $result[] = $entry;
        }

        $this->assertEquals(["a", "b"], $result);
    }

    public function testCons() {
        $set = new Set(["a"]);
        $set->cons("b");

        $this->assertEquals(new Set(["a", "b"]), $set);
    }

    public function testFirst() {
        $set = new Set(["a", "b"]);
        $this->assertEquals("a", $set->first());
    }

    public function testFirstEmpty() {
        $set = new Set([]);
        $this->assertNull($set->first());
    }

    public function testCdr() {
        $set = new Set(["a", "b"]);
        $this->assertEquals(new PhelArray(["b"]), $set->cdr());
    }

    public function testCdrEmpty() {
        $set = new Set([]);
        $this->assertEquals(null, $set->cdr());
    }

    public function testRest() {
        $set = new Set(["a", "b"]);
        $this->assertEquals(new PhelArray(["b"]), $set->rest());
    }

    public function testRestEmpty() {
        $set = new Set([]);
        $this->assertEquals(new PhelArray([]), $set->rest());
    }

    public function testPush() {
        $set = new Set(["a"]);
        $set->push("b");
        $set->push("b");
        $this->assertEquals(new Set(["a", "b"]), $set);
    }

    public function testPushDifferentTypes() {
        $set1 = new Set(["a"]);
        $set2 = new Set(["b"]);
        $date = new \DateTime();

        $set1->push(1);
        $set1->push($set2);
        $set1->push($date);
        $this->assertEquals(new Set(["a", 1, $set2, $date]), $set1);
    }

    public function testConcat() {
        $set1 = new Set(["a", "b"]);
        $set2 = new Set(["b", "c"]);
        $set1->concat($set2);

        $this->assertEquals(new Set(["a", "b", "c"]), $set1);
        $this->assertEquals(new Set(["b", "c"]), $set2);
    }

    public function testIntersection() {
        $set1 = new Set(["a", "b"]);
        $set2 = new Set(["b", "c"]);
        $intersection = $set1->intersection($set2);

        $this->assertEquals(new Set(["b"]), $intersection);
        $this->assertEquals(new Set(["a", "b"]), $set1);
        $this->assertEquals(new Set(["b", "c"]), $set2);
    }

    public function testDifference() {
        $set1 = new Set(["a", "b"]);
        $set2 = new Set(["b", "c"]);
        $intersection = $set1->difference($set2);

        $this->assertEquals(new Set(["a"]), $intersection);
        $this->assertEquals(new Set(["a", "b"]), $set1);
        $this->assertEquals(new Set(["b", "c"]), $set2);
    }

    public function testEquals() {
        $set1 = new Set(["a", "b"]);
        $set2 = new Set(["a", "b"]);
        $set3 = new Set(["b", "c"]);

        $this->assertTrue($set1->equals($set2));
        $this->assertTrue($set2->equals($set1));
        $this->assertTrue($set1->equals($set1));
        $this->assertFalse($set1->equals($set3));
        $this->assertFalse($set3->equals($set1));
    }

    public function testToPhpArray() {
        $set = new Set(["a", "b"]);
        $this->assertEquals(["a" => "a", "b" => "b"], $set->toPhpArray());
    }

    public function testToString() {
        $set = new Set(["a", "b"]);
        $this->assertEquals('(set "a" "b")', $set->__toString());
    }
}
