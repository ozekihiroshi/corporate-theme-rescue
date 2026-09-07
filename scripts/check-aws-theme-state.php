<?php
// Read-only evidence for the explicitly authorized existing AWS demo.
require '/var/www/html/wp-load.php';
if (rtrim(home_url(), '/') !== 'https://wp.ceri.link' || get_stylesheet() !== 'ozeki-corporate') {
    throw new RuntimeException('Unexpected site or active theme');
}
global $wpdb;
$result = [];
foreach (['posts', 'postmeta', 'term_relationships'] as $table) {
    $name = $wpdb->$table;
    $rows = $wpdb->get_results("SELECT * FROM `$name`", ARRAY_A);
    if ($wpdb->last_error) { throw new RuntimeException('Snapshot query failed'); }
    $hashes = array_map(static fn($row) => hash('sha256', serialize($row)), $rows);
    sort($hashes);
    $result[$table] = ['count' => count($rows), 'sha256' => hash('sha256', implode('', $hashes))];
}
foreach (['home','siteurl','show_on_front','page_on_front','page_for_posts','stylesheet','template','active_plugins','theme_mods_ozeki-corporate'] as $key) {
    $result['options'][$key] = hash('sha256', serialize(get_option($key)));
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
