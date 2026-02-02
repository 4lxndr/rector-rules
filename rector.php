<?php

declare(strict_types=1);

use Alxndr\Rector\SortGettersSettersByPropertyOrderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        SortGettersSettersByPropertyOrderRector::class => [
            __DIR__ . '/src/SortGettersSettersByPropertyOrderRector.php',
        ],
    ])
    ->withRules([
        SortGettersSettersByPropertyOrderRector::class,
    ]);
