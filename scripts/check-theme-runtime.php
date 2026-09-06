<?php
if (!defined('ABSPATH')) { exit(1); }
$theme=wp_get_theme();
if ($theme->get_stylesheet()!=='ozeki-corporate' || !wp_is_block_theme()) { throw new RuntimeException('Wrong theme'); }
$base=json_decode(file_get_contents(get_stylesheet_directory().'/theme.json'),true,512,JSON_THROW_ON_ERROR);
json_decode(file_get_contents(get_stylesheet_directory().'/styles/japanese-refined.json'),true,512,JSON_THROW_ON_ERROR);
if (!WP_Theme_JSON_Resolver::get_merged_data()->get_settings()) { throw new RuntimeException('Missing theme settings'); }
$templates=get_block_templates([], 'wp_template');
$GLOBALS['post']=get_post(1);
setup_postdata($GLOBALS['post']);
foreach ($templates as $template) {
    $blocks=parse_blocks($template->content);
    if (!$blocks) { throw new RuntimeException('Empty template '.$template->id); }
    do_blocks($template->content);
}
$patterns=WP_Block_Patterns_Registry::get_instance()->get_all_registered();
$count=0;
foreach ($patterns as $pattern) {
    if (str_starts_with($pattern['name'],'ozeki-corporate/')) { do_blocks($pattern['content']); ++$count; }
}
if ($count<1) { throw new RuntimeException('No theme patterns'); }
echo 'RUNTIME_OK PHP='.PHP_VERSION.' WP='.get_bloginfo('version').' templates='.count($templates).' patterns='.$count.PHP_EOL;
