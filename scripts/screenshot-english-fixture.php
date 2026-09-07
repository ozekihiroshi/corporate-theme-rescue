<?php
// Temporary local-only screenshot fixture. Copy into the disposable 8091 site,
// capture, then remove. No database changes or saved template modifications.
if (!in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost:8091', '127.0.0.1:8091'], true)) {
    http_response_code(403);
    exit;
}
require __DIR__ . '/wp-load.php';
switch_to_locale('en_US');
add_filter('pre_option_blogname', static fn() => 'Example Studio');
$content = do_blocks(file_get_contents(get_theme_file_path('templates/front-page.html')));
?><!doctype html>
<html lang="en-US"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?>
<div class="wp-site-blocks"><?php echo $content; // Trusted theme block rendering, not request data. ?></div>
<?php wp_footer(); ?></body></html>
