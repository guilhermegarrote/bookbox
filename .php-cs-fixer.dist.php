<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        'app',
        'config',
        'database',
        'routes',
        'tests',
        'bootstrap',
    ])
    ->exclude([
        'storage',
        'vendor',
        'node_modules',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setFinder($finder)
    ->setRules([
        // === BASE STANDARDS ===
        '@PSR12' => true,
        '@PhpCsFixer' => true, // includes a solid set of modern best practices

        // === DOCUMENTATION (PSR-19) ===
        'phpdoc_summary' => true, // requires a summary on the first line
        'phpdoc_align' => ['align' => 'left'], // avoids excessive indentation
        'phpdoc_scalar' => true, // forces scalar types to lowercase
        'phpdoc_separation' => true, // inserts blank lines between tag groups
        'phpdoc_order' => true, // orders @param, @return, etc.
        'phpdoc_indent' => true,
        'phpdoc_var_without_name' => false,
        'phpdoc_no_empty_return' => true, // removes redundant "@return void"
        'no_empty_phpdoc' => true,

        // === CLEAN AND CONSISTENT CODE ===
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'no_blank_lines_after_phpdoc' => true,
        'blank_line_after_namespace' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'if', 'foreach', 'while', 'do', 'switch', 'try'],
        ],
        'single_blank_line_at_eof' => true,

        // === TYPING AND SAFETY ===
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'fully_qualified_strict_types' => true, // uses FQCNs in types (total clarity)
        'no_unreachable_default_argument_value' => true,

        // === ARRAYS AND STRINGS ===
        'array_syntax' => ['syntax' => 'short'], // [] instead of array()
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'single_quote' => true,
        'concat_space' => ['spacing' => 'one'], // "Hello " . $name

        // === CLASSES, METHODS AND FUNCTIONS ===
        'class_definition' => [
            'multi_line_extends_each_single_line' => true,
            'single_item_single_line' => true,
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'method_chaining_indentation' => true, // ideal for Eloquent
        'visibility_required' => ['elements' => ['property', 'method']], // enforces public/private
        'no_null_property_initialization' => true,
        'no_trailing_whitespace_in_comment' => true,

        // === PERFORMANCE / MICRO-OPTIMIZATIONS ===
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
        ],
        'combine_consecutive_unsets' => true,
        'no_useless_return' => true,

        // === READABILITY ===
        'blank_line_after_opening_tag' => true,
        'no_whitespace_in_blank_line' => true,
        'line_ending' => true,
        'indentation_type' => true,

        // === LARAVEL-CONSCIOUS ===
        'no_superfluous_elseif' => true,
        'simplified_null_return' => false,
        'yoda_style' => false,
        'phpdoc_to_comment' => false,

        // === TESTS AND ASSERTS ===
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'php_unit_strict' => true, // enforces strict asserts
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'self',
        ],
    ]);
