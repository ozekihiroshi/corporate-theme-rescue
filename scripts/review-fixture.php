<?php
// Temporary localhost-only endpoint. Uses the installed ZIP's file templates,
// bypassing saved demo template parts; remove from the document root after tests.
if (!in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost:8090', '127.0.0.1:8090'], true)) { http_response_code(403); exit; }
require __DIR__.'/wp-load.php';
$ids=get_option('oc_review_fixture_ids', []);
$key=in_array($_GET['case'] ?? '', ['closed','protected'], true) ? $_GET['case'] : 'parent';
if (empty($ids[$key])) { http_response_code(404); exit; }
wp('page_id='.(int)$ids[$key]);
add_filter('pre_render_block', function($pre, $block) {
    if ($block['blockName']==='core/template-part') {
        $slug=$block['attrs']['slug'] ?? '';
        if (in_array($slug,['header','footer'],true)) { return do_blocks(file_get_contents(get_template_directory().'/parts/'.$slug.'.html')); }
    }
    return $pre;
}, 10, 2);
if (($_GET['menu'] ?? '')==='custom') {
    $link=static function($name) use ($ids) { return esc_url(get_permalink($ids[$name])); };
    $content='<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} -->';
    $content.='<!-- wp:navigation-submenu '.wp_json_encode(['label'=>'Review parent','url'=>$link('parent')]).' -->';
    $content.='<!-- wp:navigation-submenu '.wp_json_encode(['label'=>'Review child','url'=>$link('child')]).' -->';
    $content.='<!-- wp:navigation-link '.wp_json_encode(['label'=>'Review grandchild','url'=>$link('grandchild')]).' /-->';
    $content.='<!-- /wp:navigation-submenu --><!-- /wp:navigation-submenu --><!-- /wp:navigation -->';
    $registry=WP_Block_Patterns_Registry::get_instance();
    $registry->unregister('ozeki-corporate/navigation');
    $registry->register('ozeki-corporate/navigation', ['title'=>'Review navigation', 'content'=>$content]);
}
$GLOBALS['_wp_current_template_content']=file_get_contents(get_template_directory().'/templates/page.html');
$GLOBALS['_wp_current_template_id']='ozeki-corporate//page';
require ABSPATH.WPINC.'/template-canvas.php';
