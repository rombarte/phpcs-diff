<?php

namespace App;

class AppFacade
{
    public static function run()
    {
        $gitDiff = `git diff --unified=0 --minimal -b` ?? '';
        $phpcsDiff = `vendor/bin/phpcs` ?? '';

        $a = (new GitDifferences($gitDiff))->getDifferences();
        $b = (new PhpcsDifferences($phpcsDiff))->getDifferences();

        foreach ($b as $k => $item) {
            foreach ($item as $k2 => $item2) {
                $file = $k;
                $line = $item2['from'];

                echo $k . ' ' . $line . PHP_EOL;
            }
        }
    }
}