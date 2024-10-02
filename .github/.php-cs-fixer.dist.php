<?php
try {
    if (file_exists(__DIR__ . '/../storage/caches/vendor/autoload.php')) {
        require(__DIR__ . '/../storage/caches/vendor/autoload.php');
    } else {
        die('Hello, it looks like you did not run: "composer install --no-dev --optimize-autoloader". Please run that and refresh the page');
    }
} catch (Exception $e) {
    die('Hello, it looks like you did not run: composer install --no-dev --optimize-autoloader Please run that and refresh');
}


use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<'HEADER'
This file is part of MythicalSystemsFramework.
Please view the LICENSE file that was distributed with this source code.

(c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
(c) NaysKutzu <nayskutzu.xyz> - All rights reserved
(c) Cassian Gherman <nayskutzu.xyz> - All rights reserved

You should have received a copy of the MIT License
along with this program. If not, see <https://opensource.org/licenses/MIT>.
HEADER;


$finder = (new Finder())
    ->in(__DIR__ . '/../')
    ->exclude([
        'vendor',
        'node_modules',
        '.github',
        '.vscode',
        '.git',
        'docs',
        'caches',
        'logs',
    ])
    ->notName(['_ide_helper*']);

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRules([
        '@Symfony' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@PSR12' => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'heredoc_to_nowdoc' => true,
        'no_alias_functions' => true,
        'no_unreachable_default_argument_value' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'length',
        ],
        'phpdoc_align' => [
            'align' => 'left',
            'tags' => [
                'param',
                'property',
                'return',
                'throws',
                'type',
                'var',
            ],
        ],
        'header_comment' => ['header' => $header],
        'phpdoc_order' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_no_package' => true,
        'phpdoc_separation' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'phpdoc_var_annotation_correct_order' => true,
        'single_line_throw' => false,
        'single_line_comment_style' => false,
        'random_api_migration' => true,
        'ternary_to_null_coalescing' => true,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
        'ordered_class_elements' => true,
        'no_useless_else' => true,
        'no_extra_blank_lines' => true,
        'logical_operators' => true,
        'no_unused_imports' => true,
    ]);
