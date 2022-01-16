<?php

declare(strict_types=1);

namespace App\Config;

class App
{
    private string $applicationRootDirectory;

    public function __construct(string $applicationRootDirectory = '/var/www/html/')
    {
        $this->applicationRootDirectory = $applicationRootDirectory;
    }

    public function getApplicationRootDirectory(): string
    {
        return $this->applicationRootDirectory;
    }
}
