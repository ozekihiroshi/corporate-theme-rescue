<?php
/** Run in the disposable audit database only. */
if (!defined('ABSPATH')) { exit(1); }
$created=[];
$assert=static function($ok,$message) { if (!$ok) { throw new RuntimeException($message); } };
try {
    foreach (['open','closed','protected','empty'] as $mode) {
        $id=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Review '.$mode,
            'post_content'=>'Review body','comment_status'=>in_array($mode,['closed','empty'],true)?'closed':'open',
            'post_password'=>$mode==='protected'?'local-test':''],true);
        $assert(!is_wp_error($id), 'Cannot create fixture');
        $created[]=$id;
        if ($mode!=='empty') { wp_insert_comment(['comment_post_ID'=>$id,'comment_content'=>'Existing review comment','comment_author'=>'Visitor','comment_approved'=>1]); }
        $GLOBALS['wp_query']=new WP_Query(['page_id'=>$id]);
        $GLOBALS['wp_the_query']=$GLOBALS['wp_query'];
        $GLOBALS['wp_query']->the_post();
        $html=do_blocks(file_get_contents(get_template_directory().'/templates/page.html'));
        $assert(str_contains($html,'id="commentform"')===($mode==='open'), 'Page comment form: '.$mode);
        $assert(str_contains($html,'Existing review comment')===in_array($mode,['open','closed'],true), 'Page comments visibility: '.$mode);
        if ($mode==='open') { $assert(str_contains($html,'comment-reply-link'), 'Reply link missing'); }
    }
    echo "REVIEW_COMMENTS_OK open_closed_empty_protected\n";
} finally {
    wp_reset_postdata();
    foreach ($created as $id) { wp_delete_post($id,true); }
}
require_once ABSPATH.WPINC.'/pomo/mo.php';
$catalog=new MO();
foreach (['Home','Search','Page not found','Built with WordPress.','No content was found.','Read more'] as $text) {
    $catalog->add_entry(new Translation_Entry(['singular'=>$text,'translations'=>['Translated '.$text]]));
}
$file=get_template_directory().'/languages/en_US.mo';
$assert(!file_exists($file), 'Do not overwrite a bundled translation');
try {
    $assert($catalog->export_to_file($file), 'Cannot write temporary catalog');
    unload_textdomain('ozeki-corporate', true);
    load_theme_textdomain('ozeki-corporate', get_template_directory().'/languages');
    foreach (['navigation'=>'Home','search-form'=>'Search','not-found'=>'Page not found','footer-credit'=>'Built with WordPress.','no-results'=>'No content was found.','post-excerpt'=>'Read more'] as $pattern=>$text) {
        ob_start(); include get_template_directory().'/patterns/'.$pattern.'.php'; $markup=ob_get_clean();
        $assert(str_contains($markup,'Translated '.$text), 'Translation not applied: '.$pattern);
        $assert(serialize_blocks(parse_blocks($markup))===$markup, 'Invalid translated markup: '.$pattern);
    }
    echo "REVIEW_TRANSLATION_OK six_patterns_local_catalog\n";
} finally {
    if (file_exists($file)) { unlink($file); }
    unload_textdomain('ozeki-corporate', true);
}
