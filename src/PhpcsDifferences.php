<?php

declare(strict_types=1);

namespace App;

use App\Config\AppConfig;

/**
 * Parse "vendor/bin/phpcs" command output
 */
class PhpcsDifferences
{
    private string $phpcsDiff;
    private AppConfig $appConfig;

    public function __construct(AppConfig $appConfig, string $phpcsDiff)
    {
        $this->appConfig = $appConfig;
        $this->phpcsDiff = $phpcsDiff;
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

            $item['from'] = (int) trim($changes);
            $item['to'] = (int) trim($changes);

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
        return explode(PHP_EOL, $this->phpcsDiff);
    }

    private function isFilenameLine(string $line): bool
    {
        return substr($line, 0, 5) === 'FILE:';
    }

    private function extractFilename(string $line): string
    {
        $line =  trim(substr($line, 6));

        return str_replace($this->appConfig->appPath, '', $line);
    }

    private function isChangeMetaLine(string $line): bool
    {
        return (bool) strpos($line, '| ERROR |');
    }

    private function extractChanges(string $line): string
    {
        $plus = strpos($line, '|');

        return substr(
            $line,
            0,
            $plus
        );
    }
}
