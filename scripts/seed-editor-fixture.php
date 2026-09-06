<?php
if (!defined('ABSPATH') || !defined('WP_CLI') || !WP_CLI || untrailingslashit(home_url())!=='http://localhost:8090') { exit(1); }
$content=<<<'BLOCKS'
<!-- wp:paragraph --><p>Theme regression fixture / テーマ表示確認専用。既存の会社紹介や投稿とは独立したテスト文書です。</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">地域と企業のエネルギー課題を計測から分析そして運用改善まで一貫して支援するための長い日本語見出し</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>日本語の句読点、括弧（補足）、<strong>強調</strong>、<em>English emphasis</em>、<a href="https://example.com/">説明的なリンク</a>を確認します。LongUnbrokenIdentifier012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789。</p><!-- /wp:paragraph -->
<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>最初の項目<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>入れ子の項目 / Nested item</li><!-- /wp:list-item --></ul><!-- /wp:list --></li><!-- /wp:list-item --><!-- wp:list-item --><li>二つ目の項目</li><!-- /wp:list-item --></ul><!-- /wp:list -->
<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>技術は、使い続けられてこそ価値になる。</p><!-- /wp:paragraph --><cite>架空の引用 / Example quotation</cite></blockquote><!-- /wp:quote -->
<!-- wp:table --><figure class="wp-block-table"><table><thead><tr><th>項目</th><th>説明</th><th>状態</th></tr></thead><tbody><tr><td>計測</td><td>Energy Monitoring と運用改善の関係を説明する長い文章です。</td><td>確認用</td></tr><tr><td>分析</td><td>012345678901234567890123456789012345678901234567890123456789</td><td>Sample</td></tr></tbody></table></figure><!-- /wp:table -->
<!-- wp:code --><pre class="wp-block-code"><code>https://example.com/a-very-long-path-for-testing-responsive-code-blocks/abcdefghijklmnopqrstuvwxyz0123456789abcdefghijklmnopqrstuvwxyz0123456789</code></pre><!-- /wp:code -->
<!-- wp:details --><details class="wp-block-details"><summary>確認事項を開く / Expand details</summary><!-- wp:paragraph --><p>キーボードでも開閉できることを確認します。</p><!-- /wp:paragraph --></details><!-- /wp:details -->
<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">再編集と復元</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Revision baseline A.</p><!-- /wp:paragraph -->
BLOCKS;
$id=wp_insert_post(wp_slash(['post_type'=>'page','post_title'=>'編集・復元テスト / Editor regression','post_name'=>'oc-editor-audit-'.gmdate('Ymd-His'),'post_content'=>$content,'post_status'=>'publish']),true);
if(is_wp_error($id)) throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_oc_editor_audit','1');
wp_save_post_revision($id);
$baseline=null;
foreach(wp_get_post_revisions($id) as $rev){if($rev->post_content===$content){$baseline=$rev->ID;break;}}
if(!$baseline) throw new RuntimeException('Baseline revision missing');
$modified=str_replace('Revision baseline A.','Revision edited B.',$content);
wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$modified]));
if(get_post($id)->post_content!==$modified) throw new RuntimeException('Save mismatch');
$restored=wp_restore_post_revision($baseline);
if($restored!==$id || get_post($id)->post_content!==$content) throw new RuntimeException('Restore mismatch');
echo wp_json_encode(['id'=>$id,'url'=>get_permalink($id),'baselineRevision'=>$baseline,'revisionCount'=>count(wp_get_post_revisions($id)),'saveRestore'=>'passed'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
