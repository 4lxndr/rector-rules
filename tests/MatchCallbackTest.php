<?php

declare(strict_types=1);

namespace Alxndr\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class MatchCallbackTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/FixtureMatch');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/match_rule.php';
    }
}
