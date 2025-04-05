<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Services;

use Hyperf\Command\Command;

class FileGenerator
{
    /**
     * Generate a file with the given content.
     * @param string $outputFile the path to the output file
     * @param string $contents the content to write to the file
     * @param Command $command the command instance for output
     * @param bool $force whether to force overwriting existing files
     * @return bool true if the file was generated, false otherwise
     */
    public function generateFile(string $outputFile, string $contents, Command $command, bool $force = false): bool
    {
        if (file_exists($outputFile) && ! $force) {
            $answer = $command->ask(sprintf('The file <fg=yellow>%s</> already exists. Overwrite file? [y/n/a]', $outputFile), 'n');

            if ($answer === 'a') {
                $force = true;
            } elseif ($answer !== 'y') {
                $command->line(sprintf('<fg=yellow>[SKIP]</> %s', $outputFile));
                return false;
            }
        }

        file_put_contents($outputFile, $contents);
        $command->line(sprintf('<fg=green>[OK]</> %s', $outputFile));

        return true;
    }
}
