<?php

declare(strict_types=1);

namespace App;

/**
 * Parse "git diff --unified=0 --minimal -b" command output
 */
class GitDifferences
{
    private string $gitDiff;

    public function __construct(string $gitDiff)
    {
        $this->gitDiff = $gitDiff;
    }

    public function getDifferences(): array
    {
        $diffLines = $this->getDiffAsLines();

        $modifiedFiles = [];
        $i = 0;
        $isTheSameFile = false;
        foreach ($diffLines as $gitDiffLine) {
            if ($this->isFilenameLine($gitDiffLine)) {
                $i++;
                $isTheSameFile = false;

                $modifiedFiles[$i]['file'] = $this->extractFilename($gitDiffLine);
            }

            if ($this->isChangeMetaLine($gitDiffLine)) {
                if ($isTheSameFile) {
                    $i++;
                    $modifiedFiles[$i] = $modifiedFiles[$i - 1];
                }

                $modifiedFiles[$i]['changes'] = $this->extractChanges($gitDiffLine);

                $isTheSameFile = true;
            }
        }

        foreach ($modifiedFiles as $k => &$item) {
            $changes = substr($item['changes'], 1);
            $explodedChanges = explode(',', $changes);

            if (!isset($explodedChanges[1])) {
                $item['from'] = $explodedChanges[0];
                $item['to'] = $explodedChanges[0];
            } else {
                if ($explodedChanges[1] == '0') {
                    unset($modifiedFiles[$k]);
                }

                $item['from'] = (int) $explodedChanges[0];
                $item['to'] = $explodedChanges[0] + $explodedChanges[1] - 1;
            }

            unset($item['changes']);
        }

        $assoc = [];
        foreach ($modifiedFiles as $modifiedFile) {
            $assoc[$modifiedFile['file']][] = $modifiedFile;
        }

        //    var_dump($modifiedFiles); exit;

        return $assoc;
    }

    private function getDiffAsLines(): array
    {
        return explode(PHP_EOL, $this->gitDiff);
    }

    private function isFilenameLine(string $line): bool
    {
        return substr($line, 0, 2) === '++';
    }

    private function extractFilename(string $line): string
    {
        return trim(substr($line, 4));
    }

    private function isChangeMetaLine(string $line): bool
    {
        return substr($line, 0, 2) === '@@';
    }

    private function extractChanges(string $line): string
    {
        $plus = strpos($line, '+');

        return substr(
            $line,
            $plus,
            strpos($line, '@@', $plus) - $plus - 1
        );
    }
}