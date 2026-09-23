<?php
/**
 * Test-only Japanese Refined preview for the isolated localhost:8090 site.
 * Copy to mu-plugins temporarily and request a page with oc_japanese_refined=1.
 */
if (! defined('ABSPATH') || untrailingslashit(home_url()) !== 'http://localhost:8090') {
	return;
}
if (($_GET['oc_japanese_refined'] ?? '') !== '1') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only test selector.
	return;
}
add_filter('wp_theme_json_data_user', static function (WP_Theme_JSON_Data $theme_json): WP_Theme_JSON_Data {
	$variation = json_decode((string) file_get_contents(get_theme_file_path('styles/japanese-refined.json')), true, 512, JSON_THROW_ON_ERROR);
	return $theme_json->update_with($variation);
});
