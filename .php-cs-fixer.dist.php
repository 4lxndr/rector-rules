<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_separation' => true,
        'phpdoc_summary' => true,
        'single_line_throw' => false,
        'yoda_style' => false,
        // Performance optimization: use \ prefix for native functions
        'native_function_invocation' => [
            'include' => ['@all'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
