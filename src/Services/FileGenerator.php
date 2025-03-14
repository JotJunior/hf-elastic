<?php

declare(strict_types=1);

namespace Jot\HfElastic\Services;

use Hyperf\Command\Command;

class FileGenerator
{
    /**
     * Generate a file with the given content.
     * @param string $outputFile The path to the output file.
     * @param string $contents The content to write to the file.
     * @param Command $command The command instance for output.
     * @param bool $force Whether to force overwriting existing files.
     * @return bool True if the file was generated, false otherwise.
     */
    public function generateFile(string $outputFile, string $contents, Command $command, bool $force = false): bool
    {
        if (file_exists($outputFile) && !$force) {
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
