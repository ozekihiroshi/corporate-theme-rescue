<?php
/** Disposable draft persistence test. Browser editor validation remains separate. */
if (!defined('ABSPATH') || untrailingslashit(home_url()) !== 'http://localhost:8090') {
    throw new RuntimeException('Only the local 8090 fixture is permitted.');
}
$markup = '';
foreach (['services', 'process', 'company-information', 'call-to-action'] as $slug) {
    ob_start();
    require get_template_directory() . '/patterns/' . $slug . '.php';
    $part = ob_get_clean();
    if (serialize_blocks(parse_blocks($part)) !== $part) { throw new RuntimeException('Serialization mismatch: ' . $slug); }
    $markup .= $part;
}
$id = wp_insert_post(wp_slash(['post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Pattern persistence audit', 'post_content' => $markup]), true);
if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
try {
    if (get_post($id)->post_content !== $markup) { throw new RuntimeException('Initial save differs'); }
    $edited = str_replace('Example Studio (fictional business)', '検証用の会社名・長い日本語表記', $markup);
    $result = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $edited]), true);
    if (is_wp_error($result)) { throw new RuntimeException($result->get_error_message()); }
    clean_post_cache($id);
    $saved = get_post($id)->post_content;
    if ($saved !== $edited || !str_contains(do_blocks($saved), '検証用の会社名・長い日本語表記')) {
        throw new RuntimeException('Updated save/render differs');
    }
    echo "PATTERN_PERSISTENCE_OK post=$id save/update/reload/render\n";
} finally {
    if (!wp_trash_post($id)) { throw new RuntimeException('Could not trash owned fixture ' . $id); }
    echo "Fixture moved to Trash: $id (recoverable)\n";
}
