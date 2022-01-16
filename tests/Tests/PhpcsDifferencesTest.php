<?php

declare(strict_types=1);

namespace Tests;

use App\Config\AppConfig;
use App\PhpcsDifferences;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\PhpcsDifferences
 */
class PhpcsDifferencesTest extends TestCase
{
    public function testSimpleExampleReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        FILE: /var/www/html/app/Authentication/Service/CredentialManagement.php
        ----------------------------------------------------------------------------------
        FOUND 1 ERROR AFFECTING 1 LINE
        ----------------------------------------------------------------------------------
         1 | ERROR | [x] End of line character is invalid; expected "\n" but found "\r\n"
        ----------------------------------------------------------------------------------
        PHPCBF CAN FIX THE 1 MARKED SNIFF VIOLATIONS AUTOMATICALLY
        ----------------------------------------------------------------------------------
        
        
        FILE: /var/www/html/app/Blog/Model/Post.php
        ----------------------------------------------------------------------------------
        FOUND 1 ERROR AFFECTING 1 LINE
        ----------------------------------------------------------------------------------
         1 | ERROR | [x] End of line character is invalid; expected "\n" but found "\r\n"
        ----------------------------------------------------------------------------------
        PHPCBF CAN FIX THE 1 MARKED SNIFF VIOLATIONS AUTOMATICALLY
        ----------------------------------------------------------------------------------

        Time: 4.54 secs; Memory: 18MB
        DIFF;

        $differences = new PhpcsDifferences(new AppConfig(), $diff);

        self::assertEquals(
            [
                'app/Authentication/Service/CredentialManagement.php' => [
                    [
                        'file' => 'app/Authentication/Service/CredentialManagement.php',
                        'from' => 1,
                        'to' => 1
                    ]
                ],
                'app/Blog/Model/Post.php' => [
                    [
                        'file' => 'app/Blog/Model/Post.php',
                        'from' => 1,
                        'to' => 1
                    ]
                ],
            ],
            $differences->getDifferences(),
        );
    }

    public function testLineNumberWithMultipleDigitIsCorrectlyTrimmed(): void
    {
        $diff = <<<'DIFF'
        FILE: /var/www/html/src/GitDifferences.php
        ------------------------------------------------------------------------------------
        FOUND 2 ERRORS AFFECTING 2 LINES
        ------------------------------------------------------------------------------------
           1 | ERROR | [x] End of line character is invalid; expected "\n" but found "\r\n"
         102 | ERROR | [x] Expected 1 newline at end of file; 0 found
        ------------------------------------------------------------------------------------
        PHPCBF CAN FIX THE 2 MARKED SNIFF VIOLATIONS AUTOMATICALLY
        ------------------------------------------------------------------------------------
        
        
        FILE: /var/www/html/src/PhpcsDifferences.php
        -----------------------------------------------------------------------------------
        FOUND 2 ERRORS AFFECTING 2 LINES
        -----------------------------------------------------------------------------------
          1 | ERROR | [x] End of line character is invalid; expected "\n" but found "\r\n"
         92 | ERROR | [x] Expected 1 newline at end of file; 0 found
        -----------------------------------------------------------------------------------
        PHPCBF CAN FIX THE 2 MARKED SNIFF VIOLATIONS AUTOMATICALLY
        -----------------------------------------------------------------------------------
        
        Time: 1.61 secs; Memory: 8MB
        DIFF;

        $differences = new PhpcsDifferences(new AppConfig(), $diff);

        self::assertEquals(
            [
                'src/GitDifferences.php' => [
                    [
                        'file' => 'src/GitDifferences.php',
                        'from' => 1,
                        'to' => 1,
                    ],
                    ['file' => 'src/GitDifferences.php', 'from' => 102, 'to' => 102]
                ],
                'src/PhpcsDifferences.php' => [
                    [
                        'file' => 'src/PhpcsDifferences.php',
                        'from' => 1,
                        'to' => 1,
                    ],
                    ['file' => 'src/PhpcsDifferences.php', 'from' => 92, 'to' => 92]
                ],
            ],
            $differences->getDifferences(),
        );
    }
}
