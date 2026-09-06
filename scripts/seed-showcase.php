<?php
/** Local 8089 showcase. Updates only explicitly marked demo content. */
if (!defined('ABSPATH') || !defined('WP_CLI') || !WP_CLI) { exit(1); }
if (untrailingslashit(home_url()) !== 'http://localhost:8089' || get_stylesheet() !== 'ozeki-corporate') {
    WP_CLI::error('This fixture is restricted to Ozeki Corporate on localhost:8089.');
}
require_once ABSPATH . 'wp-admin/includes/image.php';

$targets = ['page' => ['home','about','services','company','contact','news','english','design-guide'], 'post' => ['japanese-long-heading','mixed-language-monitoring','reliable-monitoring-systems'], 'wp_navigation' => ['northstar-showcase'], 'wp_template' => ['front-page','home'], 'wp_template_part' => ['header','footer']];
$existing = [];
foreach ($targets as $type => $slugs) {
    foreach ($slugs as $slug) {
        $post = get_page_by_path($slug, OBJECT, $type);
        if ($post && get_post_meta($post->ID, '_ozeki_corporate_fixture', true) !== '1') {
            WP_CLI::error("Refusing to overwrite unmarked content: {$type}/{$slug}");
        }
        if ($post) { $existing[] = $post->to_array(); }
    }
}
// Preserve the previous fixture state once, independently of post revisions.
add_option('ozeki_corporate_before_showcase', ['posts' => $existing, 'front' => get_option('page_on_front'), 'news' => get_option('page_for_posts')], '', false);
$put = static function ($type, $slug, $title, $content) {
    $old = get_page_by_path($slug, OBJECT, $type);
    $data = ['post_type'=>$type,'post_name'=>$slug,'post_title'=>$title,'post_content'=>$content,'post_status'=>'publish','post_author'=>1,'comment_status'=>'closed'];
    if ($old) { $data['ID'] = $old->ID; }
    $id = wp_insert_post(wp_slash($data), true);
    if (is_wp_error($id)) { WP_CLI::error($id->get_error_message()); }
    update_post_meta($id, '_ozeki_corporate_fixture', '1');
    if (in_array($type, ['wp_template','wp_template_part'], true)) { wp_set_object_terms($id, 'ozeki-corporate', 'wp_theme'); }
    return $id;
};
$b = static function ($name, $attrs, $html) { return '<!-- wp:'.$name.($attrs ? ' '.wp_json_encode($attrs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '').' -->'.$html.'<!-- /wp:'.$name.' -->'; };
$p = static fn($s, $class='') => $b('paragraph', $class ? ['className'=>$class] : [], '<p'.($class ? ' class="'.$class.'"' : '').'>'.$s.'</p>');
$h = static fn($s, $level=2) => $b('heading', ['level'=>$level], '<h'.$level.' class="wp-block-heading">'.$s.'</h'.$level.'>');
$group = static fn($content, $class, $wide=false) => $b('group', ['className'=>$class,'layout'=>['type'=>'constrained']]+($wide ? ['align'=>'wide'] : []), '<div class="wp-block-group '.($wide ? 'alignwide ' : '').$class.'">'.$content.'</div>');
$cols = static function ($items, $class='') use ($b) {
    $inner = '';
    foreach ($items as $item) { $inner .= $b('column', [], '<div class="wp-block-column">'.$item.'</div>'); }
    return $b('columns', ['className'=>$class,'align'=>'wide'], '<div class="wp-block-columns alignwide '.$class.'">'.$inner.'</div>');
};
$btn = static fn($text,$url) => $b('buttons', [], '<div class="wp-block-buttons">'.$b('button', [], '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="'.esc_url($url).'">'.esc_html($text).'</a></div>').'</div>');
$link = static fn($text,$slug) => '<a href="'.esc_url(str_starts_with($slug,'?') ? home_url('/').$slug : home_url('/'.$slug.'/')).'">'.esc_html($text).' →</a>';
$section = static fn($label,$title,$body,$class='') => $group($group($p($label,'oc-eyebrow').$h($title).$body,'oc-section-inner',true),'oc-section '.$class);

$photos = [];
$alts = ['solar'=>'山並みと集落を背景に並ぶ太陽光パネル。架空の地域のAI生成イメージ。','team'=>'明るいオフィスで計測計画を話し合う3人の技術者。AI生成イメージ。','monitoring'=>'木の机に置いた計測機器と、グラフを表示したノートパソコン。AI生成イメージ。'];
foreach ($alts as $key=>$alt) {
    $found = get_posts(['post_type'=>'attachment','post_status'=>'inherit','meta_key'=>'_oc_showcase_photo','meta_value'=>$key,'posts_per_page'=>1]);
    if ($found) { $photos[$key] = $found[0]->ID; continue; }
    $uploads = wp_upload_dir();
    if ($uploads['error']) { WP_CLI::error($uploads['error']); }
    $file = $uploads['path'].'/'.wp_unique_filename($uploads['path'], 'northstar-'.$key.'.jpg');
    $editor = wp_get_image_editor(__DIR__.'/assets/'.$key.'.png');
    if (is_wp_error($editor)) { WP_CLI::error($editor->get_error_message()); }
    $editor->resize(1600, 1000, false);
    $editor->set_quality(84);
    $saved = $editor->save($file, 'image/jpeg');
    if (is_wp_error($saved)) { WP_CLI::error($saved->get_error_message()); }
    $id = wp_insert_attachment(['post_title'=>'Northstar demo — '.$key,'post_excerpt'=>'架空企業のサンプル用AI生成画像 / AI-generated illustration.','post_mime_type'=>'image/jpeg','post_status'=>'inherit'], $file, 0, true);
    if (is_wp_error($id)) { WP_CLI::error($id->get_error_message()); }
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id,$file));
    update_post_meta($id,'_wp_attachment_image_alt',$alt);
    update_post_meta($id,'_oc_showcase_photo',$key);
    $photos[$key]=$id;
}
$img = static function ($key) use ($photos,$alts,$b) {
    $id=$photos[$key]; $url=wp_get_attachment_url($id);
    return $b('image',['id'=>$id,'sizeSlug'=>'full','linkDestination'=>'none'], '<figure class="wp-block-image size-full"><img src="'.esc_url($url).'" alt="'.esc_attr($alts[$key]).'" class="wp-image-'.$id.'"/></figure>');
};
$services = [
    ['solar','01 / ENERGY','エネルギー計画','設備を増やす前に、使い方を見直す。電力使用の傾向と施設の運用を整理し、無理なく続く改善計画をつくります。'],
    ['monitoring','02 / MONITORING','計測と見える化','必要な場所に、必要な計測を。センサーの選定からダッシュボードまで、現場で使える仕組みを設計します。'],
    ['team','03 / OPERATIONS','運用と人づくり','導入して終わりにしない。担当者がデータを読み、判断し、次の行動へ移せるよう、運用と学びを支えます。'],
];
$cards=[];
foreach ($services as [$photo,$label,$title,$copy]) {
    $cards[]=$group($img($photo).$group($p($label,'oc-eyebrow').$h($title,3).$p($copy).$p($link('サービスを見る','services'),'oc-text-link'),'oc-card-body'),'oc-card');
}
$serviceCards=$cols($cards,'oc-card-grid');
$steps=$cols([
    $p('01','oc-number').$h('話を聴く',3).$p('施設を訪ね、困りごとと目指す姿を一緒に整理します。'),
    $p('02','oc-number').$h('小さく確かめる',3).$p('計測や試行を通して、効果と運用負担の両面を確認します。'),
    $p('03','oc-number').$h('続く仕組みにする',3).$p('手順と判断基準を共有し、改善が続く体制へつなげます。'),
],'oc-steps');
$faq='';
foreach (['まだ課題が整理できていなくても相談できますか？'=>'はい。まずは施設の使い方や、日々気になっていることをお聞きするところから始めます。','既存の設備やシステムを活用できますか？'=>'現在の設備・データ・運用を確認し、使えるものを活かした計画を検討します。','小規模な施設でも対象になりますか？'=>'規模だけでなく、目的と運用体制に合わせて段階的な進め方を考えます。'] as $q=>$a) {
    $faq.=$b('details',[], '<details class="wp-block-details"><summary>'.esc_html($q).'</summary>'.$p($a).'</details>');
}
$cta=$section('LET’S TALK','まずは、現場の話から。',$p('計画が固まる前でも大丈夫です。いま抱えている課題と、これから実現したいことをお聞かせください。').$btn('ご相談の流れを見る',home_url('/contact/')),'oc-cta');
$hero=$group($group($cols([
    $p('NORTHSTAR ENGINEERING / DEMO','oc-eyebrow').$h('地域のエネルギーを、<br>現場の改善へ。',1).$p('計測から始める、持続可能な一歩。','oc-lead').$p('私たちは、地域と企業のエネルギー課題に向き合う技術パートナー。設備とデータ、人の知恵をつなぎ、無理なく続く改善を支えます。').$btn('私たちのサービス',home_url('/services/')).$p('架空企業のデモサイトです。写真はAI生成イメージです。','oc-demo-note'),
    $img('solar'),
],'oc-hero-columns'),'oc-section-inner',true),'oc-section oc-hero');
$about=$section('OUR APPROACH','技術は、使い続けられてこそ。',$cols([$img('team'),$h('数字の先にある、<br>現場の日常を見る。',3).$p('同じ設備でも、建物の使われ方や担当者の働き方は異なります。だからこそ、私たちは対話と観察を大切にします。').$p('新しい技術を導入することだけが目的ではありません。現場に根づく仕組みを、一緒につくること。それが私たちの仕事です。').$p($link('私たちについて','about'),'oc-text-link')],'oc-story'));
$newsCards=[];
$articles=[
    ['japanese-long-heading','地域と企業のエネルギー課題を、計測・分析・運用改善まで一貫して支援する新しい取り組みを開始しました','solar','地域の施設では、設備更新と日々の運用を別々に考えてしまうことがあります。今回の架空事例では、利用時間と電力データを重ね、現場の担当者と改善の優先順位を整理しました。'],
    ['mixed-language-monitoring','Energy Monitoring と省エネルギー運用をつなぐ実践的なデータ活用','monitoring','Energy Monitoring は、グラフをつくるだけでは完結しません。何を判断するためのデータかを明確にし、週次の確認と小さな改善につなげることが重要です。'],
    ['reliable-monitoring-systems','Designing reliable monitoring systems for long-term operations','team','A useful monitoring system begins with a clear question. Choose measurements that help people make decisions, document ownership, and make routine maintenance part of the design.'],
];
foreach ($articles as [$slug,$title,$photo,$copy]) {
    $body=$p('DEMO INSIGHT / 架空の技術記事','oc-eyebrow').$p($copy,'oc-lead').$img($photo).$h('運用を起点に設計する').$p('現場の担当者と確認する頻度、記録の残し方、異常時の連絡先を決めます。小さく始め、使いながら改善することで、仕組みを継続的に育てられます。').$h('最初の一歩').$p('まずは一週間のデータと日々の運用を並べて見てみましょう。数値の変化に理由を添えるだけでも、次に調べるべきことが見えてきます。').$p('この記事はテーマの表示確認用サンプルです。実際の事業実績ではありません。','oc-demo-note');
    $id=$put('post',$slug,$title,$body); set_post_thumbnail($id,$photos[$photo]);
    $newsCards[]=$group($img($photo).$group($p('INSIGHT / SAMPLE','oc-eyebrow').$h('<a href="'.esc_url(get_permalink($id)).'">'.esc_html($title).'</a>',3).$p($copy),'oc-card-body'),'oc-card');
}
$home=$hero.$section('WHAT WE DO','課題に合わせた、三つの支援。',$p('計画・計測・運用を分断せず、必要なところから一緒に進めます。').$serviceCards,'oc-services').$about.$section('HOW WE WORK','小さく始めて、確かな前進へ。',$steps,'oc-soft').$section('FIELD NOTE','地域施設の「見えない」を整理する。',$cols([$img('monitoring'),$p('MODEL CASE / 架空の導入例','oc-eyebrow').$h('データと対話で、運用の見直しへ。',3).$p('地域の交流施設を想定し、空調・照明の使用時間と電力データを比較。担当者が確認しやすい週次レポートと、運用を見直す手順を設計する例です。').$p('特定の顧客や削減実績を示すものではありません。','oc-demo-note')],'oc-story')).$section('NEWS & INSIGHTS','お知らせと、技術の読みもの。',$cols($newsCards,'oc-card-grid').$p($link('記事一覧へ','news'),'oc-text-link'),'oc-soft').$section('QUESTIONS','よくあるご相談。',$faq).$cta;
$homeId=$put('page','home','ホーム',$home);
$put('page','about','私たちについて',$p('OUR COMPANY','oc-eyebrow').$p('地域に寄り添い、技術を日々の力に。','oc-lead').$img('team').$h('対話から始めるエンジニアリング').$p('Northstar Engineering は、地域の施設や企業が抱えるエネルギーと運用の課題を支える架空の技術会社です。人と設備、データをつなぎ、現場が自ら改善できる状態を目指します。').$h('大切にしていること').$steps.$b('quote',[], '<blockquote class="wp-block-quote">'.$p('技術の価値は、使う人の日常が少し良くなること。').'<cite>Northstar Engineering — Demo philosophy</cite></blockquote>').$p($link('会社情報を見る','company')));
$put('page','services','サービス',$p('OUR SERVICES','oc-eyebrow').$p('課題を整理し、計測し、運用へつなぐ。','oc-lead').$serviceCards.$h('支援の進め方').$steps.$h('相談前に用意するもの').$p('施設の概要、気になる点、現在使っている設備やデータがあれば十分です。詳細が分からない段階でも、一緒に整理することを想定しています。').$h('よくあるご相談').$faq.$btn('ご相談について',home_url('/contact/')));
$table=$b('table',[], '<figure class="wp-block-table"><table><tbody><tr><th>会社名</th><td>Northstar Engineering（架空）</td></tr><tr><th>事業内容</th><td>エネルギー計画、計測システム設計、運用改善・人材育成</td></tr><tr><th>拠点</th><td>日本 / Japan（サンプル）</td></tr><tr><th>対応言語</th><td>日本語・英語 / Japanese &amp; English</td></tr><tr><th>お問い合わせ</th><td>本デモサイトからの送信はできません。</td></tr></tbody></table></figure>');
$put('page','company','会社情報',$p('COMPANY PROFILE','oc-eyebrow').$p('地域に根ざす技術パートナーを想定した、企業情報の表示例です。').$table.$img('solar').$p('会社名・事業内容・写真は、すべてテーマのサンプルとして用意したものです。','oc-demo-note'));
$put('page','contact','ご相談・お問い合わせ',$p('CONTACT','oc-eyebrow').$p('まだ言葉にならない課題から、ご一緒に。','oc-lead').$p('現状と目指す姿を伺い、必要な情報と次の一歩を整理します。下記は問い合わせ導線のサンプルであり、送信機能はありません。').$h('ご相談の際にお知らせいただきたいこと').$b('list',[], '<ul class="wp-block-list">'.$b('list-item',[],'<li>施設や組織の概要と、ご担当の業務</li>').$b('list-item',[],'<li>いま困っていること、改善したいこと</li>').$b('list-item',[],'<li>取り組みたい時期と、現在の設備・データの状況</li>').'</ul>').$h('ご相談から開始まで').$steps.$h('よくあるご相談').$faq.$p('デモ用連絡先：contact@example.com（送信しないでください）','oc-demo-note'));
$english=$p('NORTHSTAR ENGINEERING / DEMO','oc-eyebrow').$h('Practical engineering.<br>Lasting progress.').$p('We help communities and organizations turn operational challenges into workable improvements.','oc-lead').$img('solar').$h('Start with the people who use the system').$p('Technology works best when it fits the way people work. We listen, observe, and build a shared understanding before choosing equipment or writing a specification.').$cols([$h('Plan with purpose',3).$p('Understand energy use and identify practical priorities.'),$h('Measure what matters',3).$p('Design monitoring that supports real operational decisions.'),$h('Build lasting capability',3).$p('Help teams maintain the system and keep improving.')],'oc-steps').$h('From a small pilot to everyday practice').$p('We test an approach at a manageable scale, review its value with the team, and document the steps needed to keep it useful.').$img('team').$p('This is a fictional company website. All photographs are AI-generated illustrations; no actual customer outcomes are claimed.','oc-demo-note');
$put('page','english','Engineering practical progress',$group($english,'oc-english'));
$guide=$p('DESIGN REFERENCE','oc-eyebrow').$p('写真・文字・標準ブロックの組み合わせを確認するページです。','oc-lead').$h('01 — 見出しと本文').$h('地域と企業のエネルギー課題を、計測・分析・運用改善まで一貫して支援する技術パートナー',3).$p('Energy Monitoring と日本語の説明が自然に混ざる文章です。句読点、括弧（補足）、<strong>強調</strong>、<em>emphasis</em>、'.$link('通常のリンク','about').'を含みます。').$h('小さな見出しも読みやすく',4).$p('段落は明朝体、案内やラベルはゴシック体。編集画面のタイポグラフィから用途に応じて切り替えられます。').$h('02 — 写真とカード').$serviceCards.$h('03 — 表と引用').$table.$b('quote',[], '<blockquote class="wp-block-quote">'.$p('ひとつの数字から、現場との対話が始まる。').'<cite>架空企業のメッセージ</cite></blockquote>').$h('04 — 開閉できる説明').$faq.$h('05 — ボタンと検索').$btn('サービスを見る',home_url('/services/')).'<!-- wp:search {"label":"サイト内検索","buttonText":"検索"} /-->'.$h('06 — テンプレート確認').$p($link('投稿一覧','news').' / '.$link('検索結果','?s=Energy').' / '.$link('404の確認','demo-page-not-found')).$p('このページも通常のブロックで編集できます。変更前の内容はリビジョンから確認してください。','oc-demo-note');
$put('page','design-guide','デザイン見本',$guide);
$newsId=$put('page','news','お知らせ・技術記事','');
update_option('show_on_front','page'); update_option('page_on_front',$homeId); update_option('page_for_posts',$newsId);

$nav='';
foreach (['about'=>'私たちについて','services'=>'サービス','news'=>'記事','company'=>'会社情報','english'=>'English','contact'=>'ご相談'] as $slug=>$label) {
    $nav.='<!-- wp:navigation-link '.wp_json_encode(['label'=>$label,'url'=>home_url('/'.$slug.'/'),'kind'=>'custom'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).' /-->';
}
$navId=$put('wp_navigation','northstar-showcase','Northstar demo navigation',$nav);
$header=$group($group('<!-- wp:site-title {"level":0} /--><!-- wp:navigation {"ref":'.$navId.',"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} /-->','oc-header-row',true),'oc-header');
$put('wp_template_part','header','Demo header',$header);
$footer=$group($group($cols([$h('Northstar Engineering',3).$p('Engineering practical progress.<br>地域のエネルギーを、現場の改善へ。'),$p($link('会社情報','company')).$p($link('ご相談','contact')).$p($link('デザイン見本','design-guide'))],'oc-footer-columns').$p('Ozeki Corporate — 架空企業のデモサイト。写真はAI生成イメージです。実在の顧客・事業実績を示すものではありません。','oc-demo-note'),'oc-section-inner',true),'oc-footer');
$put('wp_template_part','footer','Demo footer',$footer);
$put('wp_template','front-page','Editable demo home','<!-- wp:template-part {"slug":"header","tagName":"header"} /--><!-- wp:group {"tagName":"main","layout":{"type":"default"}} --><main class="wp-block-group"><!-- wp:post-content {"layout":{"type":"default"}} /--></main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->');
$archive = '<!-- wp:query {"query":{"perPage":10,"postType":"post","inherit":true},"align":"wide"} --><div class="wp-block-query alignwide"><!-- wp:post-template -->'. $group('<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /--><!-- wp:post-title {"isLink":true,"level":2} /--><!-- wp:post-date {"fontSize":"small"} /--><!-- wp:post-excerpt {"excerptLength":40,"moreText":"続きを読む"} /-->','oc-archive-entry'). '<!-- /wp:post-template --><!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} --><!-- wp:query-pagination-previous /--><!-- wp:query-pagination-numbers /--><!-- wp:query-pagination-next /--><!-- /wp:query-pagination --></div><!-- /wp:query -->';
$put('wp_template','home','Demo news archive','<!-- wp:template-part {"slug":"header","tagName":"header"} /--><!-- wp:group {"tagName":"main","className":"oc-section","layout":{"type":"constrained"}} --><main class="wp-block-group oc-section">'.$p('NEWS & INSIGHTS','oc-eyebrow').$h('お知らせ・技術記事',1).$archive.'</main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->');
wp_get_theme()->delete_pattern_cache(); flush_rewrite_rules(false);
WP_CLI::success('Showcase ready: 8 pages, 3 articles, 3 photos, fixture-owned header/footer/front-page.');
