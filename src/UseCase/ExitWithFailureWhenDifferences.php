<?php

namespace App\UseCase;

class ExitWithFailureWhenDifferences
{
    public function run(array $gitDifferences, array $phpcsDifferences): void
    {
        $statusCode = 0;

        foreach ($phpcsDifferences as $filename => $items) {
            foreach ($items as $item) {
                $diff = $gitDifferences[$filename] ?? [];

                if (!$diff) {
                    continue;
                }

                foreach ($diff as $value) {
                    $warn = $item['from'] >= $value['from'] && $item['from'] <= $value['to'];

                    if ($warn) {
                        $statusCode = 1;
                    }
                }
            }
        }

        echo 'Exit with status code: ' . $statusCode . PHP_EOL;
        exit($statusCode);
    }
}
