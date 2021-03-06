<?php

declare(strict_types=1);

namespace Phel\Command;

use Phel\Runtime\RuntimeInterface;
use RuntimeException;

final class CommandFacade implements CommandFacadeInterface
{
    private const SUCCESS_CODE = 0;
    private const FAILED_CODE = 1;

    private string $currentDir;
    private CommandFactoryInterface $commandFactory;

    public function __construct(
        string $currentDir,
        CommandFactoryInterface $commandFactory
    ) {
        $this->currentDir = $currentDir;
        $this->commandFactory = $commandFactory;
    }

    public function executeReplCommand(): void
    {
        $this->commandFactory
            ->createReplCommand($this->loadVendorPhelRuntime())
            ->run();
    }

    public function executeRunCommand(string $fileOrPath): void
    {
        $this->commandFactory
            ->createRunCommand($this->loadVendorPhelRuntime())
            ->run($fileOrPath);
    }

    public function executeTestCommand(array $paths): void
    {
        $result = $this->commandFactory
            ->createTestCommand($this->loadVendorPhelRuntime())
            ->run($paths);

        ($result)
            ? exit(self::SUCCESS_CODE)
            : exit(self::FAILED_CODE);
    }

    public function executeFormatCommand(array $paths): void
    {
        $this->commandFactory
            ->createFormatCommand()
            ->run($paths);
    }

    private function loadVendorPhelRuntime(): RuntimeInterface
    {
        $runtimePath = $this->currentDir
            . DIRECTORY_SEPARATOR . 'vendor'
            . DIRECTORY_SEPARATOR . 'PhelRuntime.php';

        if (!file_exists($runtimePath)) {
            throw new RuntimeException('The Runtime could not be loaded from: ' . $runtimePath);
        }

        return require $runtimePath;
    }
}
