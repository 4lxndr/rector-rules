<?php

declare(strict_types=1);

use Alxndr\Rector\SortGettersSettersByPropertyOrderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(SortGettersSettersByPropertyOrderRector::class, [
        'match' => static fn (string $file): bool => !str_contains($file, 'skipped'),
    ]);
