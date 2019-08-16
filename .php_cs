<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->exclude('vendor')
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

$config = PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [ 'syntax' => 'short' ],
        'cast_spaces' => true,
        'concat_space' => [ 'spacing' => 'one' ],
        'linebreak_after_opening_tag' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_in_blank_line' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
    ])
    ->setFinder($finder);

return $config;
