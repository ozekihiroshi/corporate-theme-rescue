<?php
/** Explicit one-time addition; no theme activation, demo reseeding or replacement. */
if (!defined('WP_CLI') || !WP_CLI || home_url() !== 'https://wp.ceri.link'
    || get_stylesheet() !== 'ozeki-corporate') {
    throw new RuntimeException('Unexpected site or active theme');
}
if (get_page_by_path('theme-starter-preview') || get_posts([
    'post_type'=>'wp_template', 'name'=>'starter-preview', 'post_status'=>'any',
])) { WP_CLI::error('Preview already exists; inspect before rerunning.'); }
$option_names = ['show_on_front','page_on_front','page_for_posts','stylesheet','template','active_plugins'];
$before_options = [];
foreach ($option_names as $name) { $before_options[$name] = get_option($name); }
global $wpdb;
$before_posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} ORDER BY ID", ARRAY_A);
$baseline = [];
foreach ($before_posts as $row) { $baseline[$row['ID']] = hash('sha256', serialize($row)); }
$backup = '/tmp/oc-starter-preview-content-before.json';
if (file_exists($backup)) { WP_CLI::error('Backup path already exists.'); }
if (file_put_contents($backup, wp_json_encode(['options'=>$before_options,'posts'=>$before_posts], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    WP_CLI::error('Could not write content snapshot.');
}
chmod($backup, 0600);
$content = '';
foreach (['hero','introduction','services','strengths','latest-news','company-information','call-to-action'] as $pattern) {
    ob_start();
    include get_theme_file_path('patterns/' . $pattern . '.php');
    $content .= ob_get_clean() . "\n";
}
$template_id = wp_insert_post([
    'post_type'=>'wp_template', 'post_name'=>'starter-preview',
    'post_title'=>'Starter preview (no duplicate page title)', 'post_status'=>'publish',
    'post_content'=>'<!-- wp:template-part {"slug":"header","tagName":"header"} /--><!-- wp:group {"tagName":"main","layout":{"type":"default"}} --><main class="wp-block-group"><!-- wp:post-content {"layout":{"type":"default"}} /--></main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->',
], true);
if (is_wp_error($template_id)) { WP_CLI::error($template_id->get_error_message()); }
$terms = wp_set_object_terms($template_id, 'ozeki-corporate', 'wp_theme');
if (is_wp_error($terms)) { WP_CLI::error($terms->get_error_message()); }
$page_id = wp_insert_post(wp_slash([
    'post_type'=>'page', 'post_name'=>'theme-starter-preview',
    'post_title'=>'Theme starter preview', 'post_status'=>'publish',
    'post_content'=>$content,
    'meta_input'=>['_wp_page_template'=>'starter-preview','_oc_starter_preview'=>1],
]), true);
if (is_wp_error($page_id)) { WP_CLI::error($page_id->get_error_message()); }
foreach ($before_options as $name=>$value) {
    if (get_option($name) !== $value) { WP_CLI::error('Existing option changed: ' . $name); }
}
foreach ($baseline as $id=>$hash) {
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID=%d", $id), ARRAY_A);
    if (hash('sha256', serialize($row)) !== $hash) { WP_CLI::error('Existing post changed: ' . $id); }
}
WP_CLI::log(wp_json_encode(['page_id'=>$page_id,'template_id'=>$template_id,'url'=>get_permalink($page_id),'existing_posts_unchanged'=>count($baseline),'options_unchanged'=>array_keys($before_options),'content_snapshot'=>$backup]));
WP_CLI::success('Preview added; existing posts and selected options unchanged.');
