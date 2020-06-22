<?php

declare(strict_types=1);

namespace Phel\Exceptions;

use Exception;
use Phel\Lang\SourceLocation;

abstract class PhelCodeException extends Exception
{
    private ?SourceLocation $startLocation;
    private ?SourceLocation $endLocation;

    public function __construct(
        string $message,
        ?SourceLocation $startLocation = null,
        ?SourceLocation $endLocation = null,
        ?Exception $nestedException = null
    ) {
        parent::__construct($message, 0, $nestedException);
        $this->startLocation = $startLocation;
        $this->endLocation = $endLocation;
    }

    public function getStartLocation(): ?SourceLocation
    {
        return $this->startLocation;
    }

    public function getEndLocation(): ?SourceLocation
    {
        return $this->endLocation;
    }
}
