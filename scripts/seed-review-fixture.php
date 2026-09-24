<?php
/** Disposable content for the local 8090 review checks. */
if (!defined('ABSPATH')) { exit(1); }
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), ['localhost', '127.0.0.1'], true)) { throw new RuntimeException('Local only'); }
$ids = get_option('oc_review_fixture_ids', []);
if (($args[0] ?? '') === 'cleanup') {
    foreach (array_reverse($ids) as $id) { wp_delete_post($id, true); }
    delete_option('oc_review_fixture_ids');
    echo "REVIEW_FIXTURE_REMOVED\n";
    return;
}
if ($ids) { echo wp_json_encode($ids); return; }
foreach (['parent' => 'Review parent', 'child' => 'Review child', 'grandchild' => 'Review grandchild', 'closed' => 'Review closed', 'protected' => 'Review protected'] as $key => $title) {
    $parent = $key === 'child' ? $ids['parent'] : ($key === 'grandchild' ? $ids['child'] : 0);
    $id = wp_insert_post(['post_type'=>'page', 'post_status'=>'publish', 'post_title'=>$title,
        'post_content'=>'Review fixture body.', 'post_parent'=>$parent,
        'comment_status'=>$key === 'closed' ? 'closed' : 'open',
        'post_password'=>$key === 'protected' ? 'review-local-password' : ''], true);
    if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
    $ids[$key]=$id;
    update_option('oc_review_fixture_ids', $ids, false);
}
foreach (['parent','closed','protected'] as $key) {
    wp_insert_comment(['comment_post_ID'=>$ids[$key], 'comment_author'=>'Review visitor',
        'comment_content'=>'Existing review comment '.$key, 'comment_approved'=>1]);
}
echo wp_json_encode($ids)."\n";
