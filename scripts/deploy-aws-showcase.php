<?php
/** Explicit deployment to the existing wp.ceri.link test site. No plugin removal. */
if (!defined('WP_CLI') || !WP_CLI || home_url() !== 'https://wp.ceri.link') { exit(1); }
if (getenv('OC_SHOWCASE_TARGET') !== home_url() || get_stylesheet() !== 'ozeki-corporate') {
    WP_CLI::error('Expected explicitly selected AWS site and active release theme.');
}
$services = get_post(62);
if (!$services || $services->post_type !== 'page' || $services->post_name !== 'services') {
    WP_CLI::error('Approved replacement page identity changed.');
}
$before = [];
foreach (['blogname','blogdescription','show_on_front','page_on_front','page_for_posts','permalink_structure','timezone_string','active_plugins'] as $key) {
    $before[$key] = get_option($key);
}
add_option('oc_aws_before_showcase', ['options'=>$before, 'services'=>$services->to_array(), 'backup'=>'s3://ceri-secure-s3-storage-test/wordpress-test/backups/database/2026/09/06/db-wp_rescue-20260906-130946.sql.gz'], '', false);
// This one existing page is explicitly approved for replacement and backed up.
update_post_meta(62, '_ozeki_corporate_fixture', '1');
require __DIR__ . '/seed-showcase.php';
update_option('blogname', 'Northstar Engineering');
update_option('blogdescription', 'Ozeki Corporate — fictional company demonstration');
update_option('permalink_structure', '/%postname%/');
$variation = json_decode(file_get_contents(get_stylesheet_directory().'/styles/japanese-refined.json'), true, 512, JSON_THROW_ON_ERROR);
$style_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
$result = wp_update_post(wp_slash(['ID'=>$style_id, 'post_content'=>wp_json_encode(['version'=>3,'isGlobalStylesUserThemeJSON'=>true,'settings'=>$variation['settings']??[], 'styles'=>$variation['styles']??[]])]), true);
if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); }
wp_clean_theme_json_cache();
flush_rewrite_rules(true);
if (get_option('active_plugins') !== $before['active_plugins']) { WP_CLI::error('Active plugin list changed unexpectedly.'); }
WP_CLI::success('AWS showcase ready; Japanese Refined selected; active plugins unchanged.');
