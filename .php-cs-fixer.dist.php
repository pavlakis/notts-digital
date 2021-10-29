<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/tests/phpunit',
    ])
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'no_superfluous_phpdoc_tags' => false,
        'single_line_throw' => false,
        'strict_comparison' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => false,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => [ 'sort_algorithm' => 'length' ],
    ])
    ->setFinder($finder)
;
