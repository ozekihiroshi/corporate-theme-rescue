<?php
/** Local, explicitly scoped photo-description cleanup after the user's UI test. */
if (!defined('ABSPATH') || untrailingslashit(home_url()) !== 'http://localhost:8090') {
    throw new RuntimeException('Local 8090 only.');
}
$post = get_post(1843);
if (!$post || $post->post_status !== 'draft' || $post->post_type !== 'page') {
    throw new RuntimeException('Expected draft 1843.');
}
$descriptions = [
    1854 => '明るいオフィスで机に向かう架空の代表者（AI生成）',
    1856 => 'ノートパソコンと計測機器を置いた仕事机（AI生成の事例イメージ）',
];
$content = $post->post_content;
foreach ($descriptions as $id => $alt) {
    if (!str_contains($content, 'wp-image-' . $id) || get_post_type($id) !== 'attachment') {
        throw new RuntimeException('Expected uploaded image missing: ' . $id);
    }
}
$content = preg_replace_callback('/<img\b[^>]*>/u', function ($match) use ($descriptions) {
    $tag = new WP_HTML_Tag_Processor($match[0]);
    if (!$tag->next_tag('img')) { return $match[0]; }
    foreach ($descriptions as $id => $alt) {
        if ($tag->has_class('wp-image-' . $id)) { $tag->set_attribute('alt', $alt); }
    }
    return $tag->get_updated_html();
}, $content);
$content = str_replace(
    ['縦長画像への差し替え検証です。', '横長画像への差し替え検証です。'],
    ['AI生成の見本画像です。実在の代表者ではありません。', 'AI生成の事例イメージです。実際の顧客案件を撮影したものではありません。'],
    $content
);
if (serialize_blocks(parse_blocks($content)) !== $content) {
    throw new RuntimeException('Block serialization differs.');
}
if ($content !== $post->post_content) {
    wp_save_post_revision($post->ID);
    $result = wp_update_post(wp_slash(['ID' => $post->ID, 'post_content' => $content]), true);
    if (is_wp_error($result)) { throw new RuntimeException($result->get_error_message()); }
}
foreach ($descriptions as $id => $alt) { update_post_meta($id, '_wp_attachment_image_alt', $alt); }
clean_post_cache($post->ID);
if (get_post($post->ID)->post_content !== $content || get_post_status($post->ID) !== 'draft') {
    throw new RuntimeException('Saved content/status differs.');
}
echo "PHOTO_DESCRIPTIONS_OK draft=1843 images=1854,1856 URLs and layout unchanged\n";
