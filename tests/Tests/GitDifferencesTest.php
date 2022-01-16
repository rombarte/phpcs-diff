<?php

declare(strict_types=1);

namespace Tests;

use App\GitDifferences;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\GitDifferences
 */
class GitDifferencesTest extends TestCase
{
    public function testAddedOneNewFileReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index e69de29..4a982ee 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -0,0 +1,11 @@
        +<?php
        +
        +class Example
        +{
        +    private int $id;
        +
        +    public function __construct(int $id)
        +    {
        +        $this->id = $id;
        +    }
        +}
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals(
            ['Example.php' => [['file' => 'Example.php', 'from' => 1, 'to' => 11]]],
            $differences->getDifferences(),
        );
    }

    public function testAddedOneNewChangeReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index 4a982ee..1bcdb8b 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -4,0 +5,2 @@ class Example
        +    private const ID = 1;
        +
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals(
            ['Example.php' => [['file' => 'Example.php', 'from' => 5, 'to' => 6]]],
            $differences->getDifferences(),
        );
    }

    public function testOneDeleteReturnsNoResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index 1bcdb8b..683c90b 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -7,2 +6,0 @@ class Example
        -    private int $id;
        -
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals([], $differences->getDifferences());
    }

    public function testOneModificationReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index 1bcdb8b..5f6d1b1 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -9 +9 @@ class Example
        -    public function __construct(int $id)
        +    public function __construct(string $id)
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals(
            ['Example.php' => [['file' => 'Example.php', 'from' => 9, 'to' => 9]]],
            $differences->getDifferences(),
        );
    }

    public function testMultipleModificationReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index 1bcdb8b..11aecbb 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -7 +7 @@ class Example
        -    private int $id;
        +    private string $id;
        @@ -9 +9 @@ class Example
        -    public function __construct(int $id)
        +    public function __construct(string $id)
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals(
            [
                'Example.php' => [
                    ['file' => 'Example.php', 'from' => 7, 'to' => 7],
                    ['file' => 'Example.php', 'from' => 9, 'to' => 9],
                ]
            ],
            $differences->getDifferences(),
        );
    }

    public function testAdvancedExampleReturnsCorrectResult(): void
    {
        $diff = <<<'DIFF'
        diff --git a/Example.php b/Example.php
        index 11aecbb..7e09b8d 100644
        --- a/Example.php
        +++ b/Example.php
        @@ -5,2 +4,0 @@ class Example
        -    private const ID = 1;
        -
        @@ -12,0 +11,2 @@ class Example
        +
        +    // this is comment
        diff --git a/index.php b/index.php
        index e69de29..fb1b7ca 100644
        --- a/index.php
        +++ b/index.php
        @@ -0,0 +1,3 @@
        +<?php
        +
        +echo 1;
        DIFF;

        $differences = new GitDifferences($diff);

        self::assertEquals(
            [
                'Example.php' => [['file' => 'Example.php', 'from' => 11, 'to' => 12]],
                'index.php' => [['file' => 'index.php', 'from' => 1, 'to' => 3]],
            ],
            $differences->getDifferences(),
        );
    }
}
