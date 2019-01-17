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
        $this->title(PHP_EOL. 'Starting PHP CS Fixer...');
        $files = $this->getStagedFiles('/*.php$', false);

        if (empty($files)) {
            return;
        }

        try {
            $baseCommand = [$this->getPhpCsFixerPath(), 'fix', '--config=.php_cs.dist', '--using-cache=no', '--dry-run', '--diff', '--diff-format=udiff'];
            $command = array_merge($baseCommand, $files);
            $process = new Process($command);
            $process->run();

            $this->write($process->getOutput());

            if ($process->isSuccessful()) {
                $add = array_merge(['git', 'add'], $files);
                $process = new Process($add);
                $process->run();

                if($process->isSuccessful()) {
                    $this->success('PhpCsFixer successfully password', false);
                    return;
                }
            }
            $this->write('PhpCsFixer detected errors!');
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