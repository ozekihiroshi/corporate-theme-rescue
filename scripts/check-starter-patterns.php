<?php
/** Read-only structure checks: run with WordPress loaded, never seed content. */
if (!defined('ABSPATH')) { throw new RuntimeException('WordPress must be loaded.'); }
$names = ['hero', 'services', 'introduction', 'company-information', 'call-to-action', 'page-about', 'page-services', 'page-company', 'page-contact'];
foreach ($names as $name) {
    ob_start();
    include get_theme_file_path('patterns/' . $name . '.php');
    $markup = ob_get_clean();
    $blocks = parse_blocks($markup);
    if (str_starts_with($name, 'page-')) {
        $registered = WP_Block_Patterns_Registry::get_instance()->get_registered('ozeki-corporate/' . $name);
        if (!$registered || !in_array('page', $registered['postTypes'] ?? [], true)
            || !in_array('core/post-content', $registered['blockTypes'] ?? [], true)) {
            throw new RuntimeException('Missing page-starter registration: ' . $name);
        }
        if (str_contains($markup, '<h1') || str_contains($markup, 'wp:template-part') || str_contains($markup, 'wp:pattern ')) {
            throw new RuntimeException('Page contains title, template part or unresolved pattern: ' . $name);
        }
    }
    if (serialize_blocks($blocks) !== $markup) {
        throw new RuntimeException('Block serialization mismatch: ' . $name);
    }
    $check = function (array $items) use (&$check, $name): void {
        foreach ($items as $block) {
            if ($block['blockName'] === null && trim($block['innerHTML']) !== '') {
                throw new RuntimeException('Unexpected freeform content: ' . $name);
            }
            $check($block['innerBlocks']);
        }
    };
    $check($blocks);
    echo $name . ": parsed and serialized\n";
}
if (!is_file(get_theme_file_path('assets/images/team.png'))) {
    throw new RuntimeException('Missing starter image');
}
echo "Starter image present. No content was created or changed.\n";
echo "Editor validation, visual inspection and fresh ZIP install remain separate checks.\n";
