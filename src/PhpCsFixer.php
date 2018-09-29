<?php

namespace PhpComposter;

use Exception;
use PHPComposter\PHPComposter\BaseAction;
use Symfony\Component\Process\Process;

/**
 * Class PhpCsFixer
 * @package PhpComposter
 */
final class PhpCsFixer extends BaseAction
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
            $baseCommand = [$this->getPhpCsFixerPath(), 'fix', '--config=.php_cs.dist', '--using-cache=no'];
            $command = array_merge($baseCommand, $files);
            $process = new Process($command);
            $process->run();

            $this->write($process->getOutput());

            if ($process->isSuccessful()) {
                $process = new Process($this->gitCall('add .'));
                $process->run();

                $this->success('PhpCsFixer successfully applied', false);
                return;
            }
            $this->error('PhpCsFixer detected errors, aborting commit!', self::EXIT_ERRORS_FOUND);
        } catch (Exception $e) {
            $this->error('An error occurred trying to run PhpCsFixer: ' . PHP_EOL . $e->getMessage(), self::EXIT_WITH_EXCEPTIONS);
        }

        echo 'Running PhpCsFixer in ' . $this->root . PHP_EOL;

    }

    /**
     * @return string
     */
    private function getPhpCsFixerPath()
    {
        return 'vendor/bin/php-cs-fixer';
    }

}