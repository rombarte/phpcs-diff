<?php

namespace App\UseCase;

use App\Config;
use App\GitDifferences;
use App\PhpcsDifferences;

class ListDifferences
{
    private Config\App $config;

    public function __construct(Config\App $config)
    {
        $this->config = $config;
    }

    public function run(): void
    {
        $gitDiff = `git diff --unified=0 --minimal -b HEAD^...` ?? '';
        $phpcsDiff = `vendor/bin/phpcs` ?? '';

        $gitDifferences = (new GitDifferences($gitDiff))->getDifferences();
        $phpcsDifferences = (new PhpcsDifferences($this->config, $phpcsDiff))->getDifferences();

        echo 'Git differences:' . PHP_EOL;

        foreach ($gitDifferences as $filename => $items) {
            foreach ($items as $item) {
                $lines = $item['from'] . ' -> ' . $item['to'];

                echo $filename . ' : ' . $lines . PHP_EOL;
            }
        }

        echo 'Phpcs differences:' . PHP_EOL;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $line = $item['from'];

                echo $filename . ' : ' . $line . PHP_EOL;
            }
        }

        echo 'Warnings:' . PHP_EOL;

        $code = 0;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $diff = $gitDifferences[$filename] ?? [];

                if (!$diff) {
                    continue;
                }

                foreach ($diff as $value) {
                    $warn = $item['from'] >= $value['from'] && $item['from'] <= $value['to'];

                    if ($warn) {
                        $code = 1;
                    }

                    echo $filename . ' : ' . ($warn ? 'warning' : 'out of scope');
                }
            }
        }

        exit($code);
    }
}
