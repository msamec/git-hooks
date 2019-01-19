<?php

namespace PhpComposter;

use Exception;
use PHPComposter\PHPComposter\BaseAction;
use Symfony\Component\Process\Process;

/**
 * Class PhpStan
 * @package PhpComposter
 */
final class PhpStan extends BaseAction
{
    public const EXIT_ERRORS_FOUND = 1;

    public const EXIT_WITH_EXCEPTIONS = 2;

    public function preCommit(): void
    {
        $this->title(PHP_EOL. 'Starting PHPStan...');
        $files = $this->getStagedFiles('/*.php$', false);
        if (empty($files)) {
            return;
        }

        try {
            $baseCommand = [$this->getPhpStanPath(), 'analyze', '--level', 'max'];
            $command = array_merge($baseCommand, $files);
            $process = new Process($command);
            $process->run();

            $this->write($process->getOutput());

            if ($process->isSuccessful()) {
                $this->success('PHPStan detected no errors.', false);
                return;
            }
            $this->write('PHPStan detected errors!');
        } catch (Exception $e) {
            $this->error('An error occurred trying to run PHPStan: ' . PHP_EOL . $e->getMessage(), self::EXIT_WITH_EXCEPTIONS);
        }

        echo 'Running PHPStan in ' . $this->root . PHP_EOL;
    }

    /**
     * @return string
     */
    private function getPhpStanPath(): string
    {
        return 'vendor/bin/phpstan';
    }

}