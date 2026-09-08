<?php

declare(strict_types=1);

/**
 * How PHP is formatted in this repository.
 *
 * PSR-12 is the base, and the rules under it are the ones a team argues about
 * otherwise: where a blank line goes, how imports are sorted, whether a comma
 * trails. Run "composer format" or let your editor do it when you save.
 *
 * This decides layout, not content. Nothing here changes what the code does.
 */

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/bin', __DIR__ . '/public', __DIR__ . '/src', __DIR__ . '/views'])
    ->append([__DIR__ . '/bootstrap.php', __DIR__ . '/config.php']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        // One blank line between the parts of a class, and none where it would
        // only push things apart.
        'blank_line_before_statement' => [
            'statements' => ['declare', 'return', 'throw', 'try', 'if', 'foreach', 'for', 'while', 'switch'],
        ],
        'class_attributes_separation' => [
            'elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one'],
        ],
        'no_blank_lines_after_class_opening' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'use'],
        ],
        'single_line_empty_body' => true,

        // Imports: one per line, alphabetical, and none that nothing uses.
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => false, 'import_functions' => false],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],

        // Types and modern syntax, so the editor keeps helping.
        'declare_strict_types' => true,
        'modernize_types_casting' => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced'],
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_class_elements' => [
            'order' => ['use_trait', 'constant_public', 'constant_protected', 'constant_private', 'property_public', 'property_protected', 'property_private', 'construct', 'method_public', 'method_protected', 'method_private'],
        ],
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'match', 'parameters']],
        'visibility_required' => ['elements' => ['const', 'method', 'property']],

        // Docblocks stay short: no padding, no empty tags, no repeated types.
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        // Deliberately off. A template lists its @var inputs in a column so you
        // can read them, and how wide that column should be is a judgement the
        // person writing it makes, not a rule.
        'phpdoc_align' => false,
        'phpdoc_indent' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => ['groups' => [['param'], ['return'], ['throws'], ['var']]],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,

        'concat_space' => ['spacing' => 'one'],
        'single_quote' => true,
    ])
    ->setFinder($finder);
