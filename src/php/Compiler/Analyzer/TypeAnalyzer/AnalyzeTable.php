<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer\TypeAnalyzer;

use Phel\Compiler\Analyzer\Ast\TableNode;
use Phel\Compiler\Analyzer\Environment\NodeEnvironmentInterface;
use Phel\Lang\AbstractType;
use Phel\Lang\Table;

final class AnalyzeTable
{
    use WithAnalyzerTrait;

    public function analyze(Table $table, NodeEnvironmentInterface $env): TableNode
    {
        $keyValues = [];
        $kvEnv = $env->withContext(NodeEnvironmentInterface::CONTEXT_EXPRESSION);

        /** @var AbstractType|string|float|int|bool|null $value */
        foreach ($table as $key => $value) {
            /** @var AbstractType|string|float|int|bool|null $key */
            $keyValues[] = $this->analyzer->analyze($key, $kvEnv);
            $keyValues[] = $this->analyzer->analyze($value, $kvEnv);
        }

        return new TableNode($env, $keyValues, $table->getStartLocation());
    }
}
