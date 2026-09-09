<?php
// Run only in the isolated theme audit installation.
if (!defined('ABSPATH')) { exit(1); }
require_once get_stylesheet_directory() . '/inc/getting-started.php';
$previous = get_current_user_id();
wp_set_current_user(1);
try {
    ob_start();
    ozeki_corporate_render_guide();
    $html = ob_get_clean();
    foreach (['Use Pages for About', 'Write a news post', 'Review posts', '投稿と固定ページ', '下書きか非公開', 'ozeki-corporate-guide-ja'] as $text) {
        if (!str_contains($html, $text)) { throw new RuntimeException('Missing guide text: ' . $text); }
    }
    foreach (['post-new.php', 'edit.php', 'site-editor.php'] as $path) {
        if (!str_contains($html, esc_url(admin_url($path)))) { throw new RuntimeException('Missing guide URL: ' . $path); }
    }
    if (!str_contains($html, esc_url(get_parent_theme_file_uri('GETTING-STARTED.md')))) {
        throw new RuntimeException('Missing complete guide');
    }
    echo "GUIDE_RUNTIME_OK english_japanese_links\n";
} finally { wp_set_current_user($previous); }
