<?php

declare(strict_types=1);

use Alxndr\Rector\SortGettersSettersByPropertyOrderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        SortGettersSettersByPropertyOrderRector::class,
    ]);
