<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude([
        'config',
        'var',
    ])
    ->notPath([
        'public/index.php',
        'tests/bootstrap.php',
    ])
;

return new PhpCsFixer\Config()
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'phpdoc_summary' => false,
        'yoda_style' => false,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_separation' => [
            'groups' => [['param', 'return', 'throws']],
        ],
        'phpdoc_align' => ['align' => 'left'],
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => false],
        'php_unit_method_casing' => false,
    ])
    ->setFinder($finder)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
;
