<?php

use App\UseCase;
use App\Config;

require 'vendor/autoload.php';

$applicationRootPath = getcwd() . '/';

$config = new Config\App($applicationRootPath);

(new UseCase\ListDifferences($config))->run();
