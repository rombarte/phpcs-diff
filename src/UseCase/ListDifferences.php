<?php

namespace App\UseCase;

class ListDifferences
{
    public function run(array $gitDifferences, array $phpcsDifferences): void
    {
        echo 'Git differences:' . PHP_EOL;
        echo '================' . PHP_EOL;

        foreach ($gitDifferences as $filename => $items) {
            foreach ($items as $item) {
                $lines = $item['from'] . ' -> ' . $item['to'];

                echo $filename . ': ' . $lines . PHP_EOL;
            }
        }

        echo 'Phpcs differences:' . PHP_EOL;
        echo '==================' . PHP_EOL;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $line = $item['from'];

                echo $filename . ': ' . $line . PHP_EOL;
            }
        }

        echo 'Warnings:' . PHP_EOL;
        echo '=========' . PHP_EOL;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $diff = $gitDifferences[$filename] ?? [];

                if (!$diff) {
                    continue;
                }

                foreach ($diff as $value) {
                    $warn = $item['from'] >= $value['from'] && $item['from'] <= $value['to'];

                    echo $filename . ' : ' . ($warn ? '[!] Warning' : '[v] Out of scope') . PHP_EOL;
                }
            }
        }
    }
}
