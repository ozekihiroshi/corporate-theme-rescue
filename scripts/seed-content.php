<?php

if (! defined('ABSPATH')) {
	exit(1);
}

$upsert = static function (string $type, string $slug, string $title, string $content, int $order = 0): int {
	$existing = get_page_by_path($slug, OBJECT, $type);
	$data = [
		'post_type' => $type,
		'post_name' => $slug,
		'post_title' => $title,
		'post_content' => $content,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_order' => $order,
		'comment_status' => 'closed',
	];
	if ($existing instanceof WP_Post) {
		$data['ID'] = $existing->ID;
	}
	$post_id = wp_insert_post(wp_slash($data), true);
	if (is_wp_error($post_id)) {
		throw new RuntimeException($post_id->get_error_message());
	}
	update_post_meta($post_id, '_ozeki_corporate_fixture', '1');
	return $post_id;
};

update_option('blogname', 'Northstar Engineering');
update_option('blogdescription', 'Engineering practical progress — 技術で持続的な前進を支える');
update_option('timezone_string', 'Asia/Tokyo');
update_option('permalink_structure', '/%postname%/');

$page_content = [
	'about' => [
		'About Us — 私たちについて',
		'<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">We help organizations turn complex operational data into reliable decisions. 私たちは、現場の課題を丁寧に整理し、計測・分析・改善を実行可能な形へつなげます。</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">地域と企業のエネルギー課題を、計測・分析・運用改善まで一貫して支援する技術パートナー</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Long Japanese headings, mixed Japanese and Latin text, punctuation（括弧・句読点）、そして改行位置を確認するための実用的なサンプルです。</p><!-- /wp:paragraph -->',
	],
	'services' => [
		'Services — サービス',
		'<!-- wp:heading --><h2 class="wp-block-heading">From measurement to measurable improvement</h2><!-- /wp:heading --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Monitoring design</h3><!-- /wp:heading --><!-- wp:paragraph --><p>計測対象、更新間隔、運用体制を整理し、必要なデータを継続的に取得できる構成を設計します。</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Data analysis</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Energy Monitoring と省エネルギー運用をつなぐ実践的なデータ活用を支援します。</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
	],
	'company' => [
		'Company Information — 会社情報',
		'<!-- wp:table --><figure class="wp-block-table"><table><tbody><tr><th>Company / 会社名</th><td>Northstar Engineering</td></tr><tr><th>Location / 所在地</th><td>Tokyo, Japan</td></tr><tr><th>Business / 事業内容</th><td>Engineering consulting, monitoring systems, and operational improvement</td></tr></tbody></table></figure><!-- /wp:table -->',
	],
	'contact' => [
		'Contact — お問い合わせ',
		'<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">Tell us about the issue you want to solve. 解決したい課題、現在の状況、ご希望の時期をお知らせください。</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>Email:</strong> contact@example.com</p><!-- /wp:paragraph -->',
	],
];

$home_id = $upsert('page', 'home', 'Home', '<!-- wp:paragraph --><p>Corporate front page fixture.</p><!-- /wp:paragraph -->', 0);
$order = 1;
foreach ($page_content as $slug => [$title, $content]) {
	$upsert('page', $slug, $title, $content, $order++);
}
$news_id = $upsert('page', 'news', 'News & Insights — お知らせ・技術記事', '', $order);
update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);
update_option('page_for_posts', $news_id);

$posts = [
	['地域と企業のエネルギー課題を、計測・分析・運用改善まで一貫して支援する新しい取り組みを開始しました', 'japanese-long-heading', '<!-- wp:paragraph --><p>長い日本語見出しに空白がなくても、画面幅に応じて自然に改行されることを確認します。</p><!-- /wp:paragraph -->'],
	['Energy Monitoring と省エネルギー運用をつなぐ実践的なデータ活用', 'mixed-language-monitoring', '<!-- wp:paragraph --><p>English terminology and 日本語の説明が同じ段落や見出しに含まれる場合の文字間隔、太さ、行高を確認します。</p><!-- /wp:paragraph -->'],
	['Designing reliable monitoring systems for long-term operations', 'reliable-monitoring-systems', '<!-- wp:paragraph --><p>A maintainable monitoring system begins with clear operational ownership, useful measurements, and realistic review cycles.</p><!-- /wp:paragraph -->'],
];
foreach ($posts as [$title, $slug, $content]) {
	$upsert('post', $slug, $title, $content);
}

wp_get_theme()->delete_pattern_cache();
flush_rewrite_rules(false);
fwrite(STDOUT, "fixture_pages=6\nfixture_posts=3\nresult=success\n");
