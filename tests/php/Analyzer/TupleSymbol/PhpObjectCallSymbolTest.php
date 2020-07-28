<?php

declare(strict_types=1);

namespace PhelTest\Analyzer\TupleSymbol;

use Phel\Analyzer;
use Phel\Analyzer\TupleSymbol\PhpObjectCallSymbol;
use Phel\Ast\MethodCallNode;
use Phel\Ast\PhpClassNameNode;
use Phel\Ast\PropertyOrConstantAccessNode;
use Phel\Exceptions\PhelCodeException;
use Phel\GlobalEnvironment;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;
use Phel\NodeEnvironment;
use PHPUnit\Framework\TestCase;

final class PhpObjectCallSymbolTest extends TestCase
{
    private Analyzer $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new Analyzer(new GlobalEnvironment());
    }

    public function testTupleWithoutArgument(): void
    {
        $this->expectException(PhelCodeException::class);
        $this->expectExceptionMessage("Exactly two arguments are expected for 'php/::");

        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_STATIC_CALL),
            Symbol::create('\\')
        );

        (new PhpObjectCallSymbol($this->analyzer, $isStatic = true))
            ->analyze($tuple, NodeEnvironment::empty());
    }

    public function testTupleWithWrongSymbol(): void
    {
        $this->expectException(PhelCodeException::class);
        $this->expectExceptionMessage("Second argument of 'php/-> must be a Tuple or a Symbol");

        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_CALL),
            Symbol::create('\\'),
            ''
        );
        (new PhpObjectCallSymbol($this->analyzer, $isStatic = false))
            ->analyze($tuple, NodeEnvironment::empty());
    }

    public function testIsStatic(): void
    {
        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_CALL),
            Symbol::create('\\'),
            Symbol::create('')
        );
        $objCallNode = (new PhpObjectCallSymbol($this->analyzer, $isStatic = true))
            ->analyze($tuple, NodeEnvironment::empty());
        self::assertSame($isStatic, $objCallNode->isStatic());
        self::assertInstanceOf(PhpClassNameNode::class, $objCallNode->getTargetExpr());
        self::assertInstanceOf(PropertyOrConstantAccessNode::class, $objCallNode->getCallExpr());
    }

    public function testIsNotStatic(): void
    {
        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_CALL),
            Symbol::create('\\'),
            Symbol::create('')
        );

        $objCallNode = (new PhpObjectCallSymbol($this->analyzer, $isStatic = false))
            ->analyze($tuple, NodeEnvironment::empty());

        self::assertSame($isStatic, $objCallNode->isStatic());
        self::assertInstanceOf(PhpClassNameNode::class, $objCallNode->getTargetExpr());
        self::assertInstanceOf(PropertyOrConstantAccessNode::class, $objCallNode->getCallExpr());
    }

    public function testTuple2ndElemIsTuple(): void
    {
        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_STATIC_CALL),
            Symbol::create('\\'),
            Tuple::create(Symbol::create(''), '', '')
        );

        $objCallNode = (new PhpObjectCallSymbol($this->analyzer, $isStatic = true))
            ->analyze($tuple, NodeEnvironment::empty());

        self::assertTrue($objCallNode->isMethodCall());
        self::assertInstanceOf(PhpClassNameNode::class, $objCallNode->getTargetExpr());
        self::assertInstanceOf(MethodCallNode::class, $objCallNode->getCallExpr());
    }

    public function testTuple2ndElemIsSymbol(): void
    {
        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_PHP_OBJECT_CALL),
            Symbol::create('\\'),
            Symbol::create('')
        );

        $objCallNode = (new PhpObjectCallSymbol($this->analyzer, $isStatic = true))
            ->analyze($tuple, NodeEnvironment::empty());

        self::assertFalse($objCallNode->isMethodCall());
        self::assertInstanceOf(PhpClassNameNode::class, $objCallNode->getTargetExpr());
        self::assertInstanceOf(PropertyOrConstantAccessNode::class, $objCallNode->getCallExpr());
    }
}