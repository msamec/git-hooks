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
    const EXIT_ERRORS_FOUND = 1;

    const EXIT_WITH_EXCEPTIONS = 2;

    public function preCommit()
    {
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
                $this->success('PHPStan detected no errors, allowing commit to proceed.', false);
                return;
            }
            $this->error('PHPStan detected errors, aborting commit!', self::EXIT_ERRORS_FOUND);
        } catch (Exception $e) {
            $this->error('An error occurred trying to run PHPStan: ' . PHP_EOL . $e->getMessage(), self::EXIT_WITH_EXCEPTIONS);
        }

        echo 'Running PHPStan in ' . $this->root . PHP_EOL;
    }

    /**
     * @return string
     */
    private function getPhpStanPath()
    {
        return 'vendor/bin/phpstan';
    }

}