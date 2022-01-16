<?php

namespace App;

use App\Config\AppConfig;

class AppFacade
{
    public static function run()
    {
        $gitDiff = `git diff --unified=0 --minimal -b` ?? '';
        $phpcsDiff = `vendor/bin/phpcs` ?? '';

        $gitDifferences = (new GitDifferences($gitDiff))->getDifferences();
        $phpcsDifferences = (new PhpcsDifferences(new AppConfig(), $phpcsDiff))->getDifferences();

        echo 'Git' . PHP_EOL;

        foreach ($gitDifferences as $filename => $items) {
            foreach ($items as $item) {
                $line = $item['from'] . '->' . $item['to'] ;

                echo $filename . ' ' . $line . PHP_EOL;
            }
        }

        echo 'Phpcs' . PHP_EOL;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $line = $item['from'] ;

                echo $filename . ' ' . $line . PHP_EOL;
            }
        }
    }
}
