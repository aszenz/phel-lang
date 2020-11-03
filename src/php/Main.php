<?php

declare(strict_types=1);

namespace Phel;

use Exception;
use Phel\Commands\CommandFactory;
use Phel\Commands\ReplCommand;
use Phel\Commands\RunCommand;
use Phel\Commands\TestCommand;

final class Main
{
    private const HELP_TEXT = <<<HELP
Usage: phel [command]

Commands:
    repl
        Start a Repl.

    run <filename-or-namespace>
        Runs a script.

    test <filename> <filename> ...
        Tests the given files. If no filenames are provided all tests in the
        test directory are executed.

    help
        Show this help message.

HELP;

    private string $currentDir;
    private string $commandName;
    private array $arguments;

    public static function create(string $currentDir, string $commandName, array $arguments = []): self
    {
        if (!getcwd()) {
            throw new Exception('Cannot get current working directory');
        }

        static::requireAutoload($currentDir);

        return new self($currentDir, $commandName, $arguments);
    }

    public static function renderHelp(): void
    {
        echo self::HELP_TEXT;
    }

    private static function requireAutoload(string $currentDir): void
    {
        $autoloadPath = $currentDir . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        if (!file_exists($autoloadPath)) {
            throw new \RuntimeException("Can not load composer's autoload file: " . $autoloadPath);
        }

        require $autoloadPath;
    }

    private function __construct(string $currentDir, string $commandName, array $arguments)
    {
        $this->currentDir = $currentDir;
        $this->commandName = $commandName;
        $this->arguments = $arguments;
    }

    public function run(): void
    {
        switch ($this->commandName) {
            case ReplCommand::NAME:
                $this->executeReplCommand();
                break;
            case RunCommand::NAME:
                $this->executeRunCommand();
                break;
            case TestCommand::NAME:
                $this->executeTestCommand();
                break;
            default:
                static::renderHelp();
        }
    }

    private function executeReplCommand(): void
    {
        $replCommand = CommandFactory::createReplCommand($this->currentDir);
        $replCommand->run();
    }

    private function executeRunCommand(): void
    {
        if (empty($this->arguments)) {
            throw new Exception('Please provide a filename or namespace as argument!');
        }

        $runCommand = CommandFactory::createRunCommand($this->currentDir);
        $runCommand->run($this->arguments[0]);
    }

    private function executeTestCommand(): void
    {
        $testCommand = CommandFactory::createTestCommand($this->currentDir);
        $result = $testCommand->run($this->currentDir, $this->arguments);
        ($result) ? exit(0) : exit(1);
    }
}
