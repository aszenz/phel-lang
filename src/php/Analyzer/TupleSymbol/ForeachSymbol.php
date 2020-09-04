<?php

declare(strict_types=1);

namespace Phel\Analyzer\TupleSymbol;

use Phel\Analyzer\WithAnalyzer;
use Phel\Ast\ForeachNode;
use Phel\Exceptions\AnalyzerException;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;
use Phel\NodeEnvironment;

final class ForeachSymbol implements TupleSymbolAnalyzer
{
    use WithAnalyzer;

    public function analyze(Tuple $tuple, NodeEnvironment $env): ForeachNode
    {
        $tupleCount = count($tuple);
        if ($tupleCount < 2) {
            throw AnalyzerException::withLocation("At least two arguments are required for 'foreach", $tuple);
        }

        if (!($tuple[1] instanceof Tuple)) {
            throw AnalyzerException::withLocation("First argument of 'foreach must be a tuple.", $tuple);
        }

        $firstArgCount = count($tuple[1]);
        if ($firstArgCount !== 2 && $firstArgCount !== 3) {
            throw AnalyzerException::withLocation("Tuple of 'foreach must have exactly two or three elements.", $tuple);
        }

        $lets = [];
        if (count($tuple[1]) === 2) {
            $keySymbol = null;

            $valueSymbol = $tuple[1][0];
            if (!($valueSymbol instanceof Symbol)) {
                $tmpSym = Symbol::gen();
                $lets[] = $valueSymbol;
                $lets[] = $tmpSym;
                $valueSymbol = $tmpSym;
            }
            $bodyEnv = $env->withMergedLocals([$valueSymbol]);
            $listExpr = $this->analyzer->analyze(
                $tuple[1][1],
                $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION)
            );
        } else {
            $keySymbol = $tuple[1][0];
            if (!($keySymbol instanceof Symbol)) {
                $tmpSym = Symbol::gen();
                $lets[] = $keySymbol;
                $lets[] = $tmpSym;
                $keySymbol = $tmpSym;
            }

            $valueSymbol = $tuple[1][1];
            if (!($valueSymbol instanceof Symbol)) {
                $tmpSym = Symbol::gen();
                $lets[] = $valueSymbol;
                $lets[] = $tmpSym;
                $valueSymbol = $tmpSym;
            }

            $bodyEnv = $env->withMergedLocals([$valueSymbol, $keySymbol]);
            $listExpr = $this->analyzer->analyze(
                $tuple[1][2],
                $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION)
            );
        }

        $bodys = [];
        for ($i = 2; $i < $tupleCount; $i++) {
            $bodys[] = $tuple[$i];
        }

        if (count($lets)) {
            $body = Tuple::create(Symbol::create(Symbol::NAME_LET), new Tuple($lets, true), ...$bodys);
        } else {
            $body = Tuple::create(Symbol::create(Symbol::NAME_DO), ...$bodys);
        }

        $bodyExpr = $this->analyzer->analyze(
            $body,
            $bodyEnv->withContext(NodeEnvironment::CONTEXT_STATEMENT)
        );

        return new ForeachNode(
            $env,
            $bodyExpr,
            $listExpr,
            $valueSymbol,
            $keySymbol,
            $tuple->getStartLocation()
        );
    }
}
