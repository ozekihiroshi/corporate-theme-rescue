<?php
// Local read-only visual fixture. Does not create pages or change settings.
if (!in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost:8090', '127.0.0.1:8090'], true)) { http_response_code(403); exit; }
require __DIR__ . '/wp-load.php';
if (($_GET['language'] ?? '') === 'ja') {
    add_filter('gettext', static function ($translated, $text, $domain) {
        if ($domain !== 'ozeki-corporate') { return $translated; }
        $labels = [
            'A message from our representative' => '代表からのご挨拶',
            'Good work begins with listening' => '一つひとつの声に耳を傾け、長く信頼いただける仕事を',
            'We believe that understanding your situation is the first step toward useful work. We take time to listen, explain our thinking and agree on a clear way forward.' => 'お客様の状況を理解することが、良い仕事の第一歩だと考えています。目の前の課題だけでなく、その背景やこれからの目標にも耳を傾け、無理のない進め方を一緒に考えます。',
            'Our aim is to be a dependable partner: thoughtful in our advice, careful in our delivery and open in our communication. We look forward to learning what matters to you.' => '丁寧な説明と着実な実行を大切にし、安心して相談できる存在を目指しています。まだ具体的になっていないお悩みでも、どうぞお聞かせください。',
            '[Role / Representative name]' => '［役職／代表者氏名］',
            'Our work in practice' => '取り組みの事例', '[Project title]' => '［現場の課題を整理し、日々の仕事を見直すための取り組み］',
            '[Introduce the project, its scope and your role. Identify the client only with permission.]' => '［どのような状況で、何を支援したかを記載します。お客様の名前や情報は、公開の許可を得てから掲載してください。］',
            '[Project period / Service provided]' => '［実施時期／支援内容］',
            'The situation' => '課題・背景', 'Our approach' => '取り組み', 'The outcome' => '結果・これから',
            '[Explain the original need or difficulty, and the constraints that mattered.]' => '［最初にどのような課題があり、どのような条件のもとで取り組んだかを説明します。］',
            '[Describe what you actually did and how you worked with the client.]' => '［実際に行った支援と、お客様とどのように協力して進めたかを説明します。］',
            '[Describe a verified result and any remaining work. Include figures only when you can substantiate them.]' => '［確認できた成果と残っている課題を記載します。数値は根拠を確認できるものだけを使います。］',
            'AI-generated illustration. Replace with your own representative or workplace photograph.' => 'AI生成の見本画像です。代表者や職場の写真に差し替えてください。',
            'AI-generated illustration. Replace with an authorized project photograph.' => 'AI生成の見本画像です。掲載許可のある事例写真に差し替えてください。',
            'Example message. Replace it with your own words and confirm permission to publish the photograph.' => '挨拶文の見本です。ご自身の言葉に置き換え、写真の掲載許可を確認してください。',
            'Optional example structure, not a client endorsement or reported result. Remove this section until you have a project you can share.' => '任意で使える事例の型です。実在の推薦や成果ではありません。公開できる事例がない場合は、このセクションを外してください。',
        ];
        return $labels[$text] ?? $translated;
    }, 10, 3);
    add_filter('wp_theme_json_data_user', static function ($data) {
        return $data->update_with(json_decode(file_get_contents(get_theme_file_path('styles/japanese-refined.json')), true));
    });
    add_filter('gettext_with_context', static function ($translated, $text, $context, $domain) {
        if ($domain !== 'ozeki-corporate') { return $translated; }
        $labels = [
            'Support from first ideas to everyday practice' => '構想から日々の運用まで、事業に寄り添うサービス',
            'Choose the support that fits your situation. Each service can stand alone or form part of a longer partnership.' => '現在の状況に合った支援をお選びください。一つの課題へのご相談から、継続的な改善まで対応します。',
            'Advisory' => 'ご相談・計画', 'Implementation' => '導入・実行', 'Ongoing support' => '継続的な支援',
            'Find a clear direction' => '現場の課題を整理し、進むべき方向を一緒に考える',
            'Turn plans into practice' => '計画を、無理なく続けられる実践へ',
            'Keep making progress' => '取り組みを振り返り、次の改善につなげる',
            'How we work together' => 'ご相談から取り組みの開始まで',
            '1. Understand' => '1. お話を伺う', '2. Plan and deliver' => '2. 計画し、実行する', '3. Review and improve' => '3. 振り返り、改善する',
            'Company information' => '会社概要', 'Company' => '会社名', 'Location' => '所在地', 'Business' => '事業内容',
            'Representative' => '代表者', 'Established' => '設立', 'Contact' => 'お問い合わせ',
            'Example Studio (fictional business)' => '株式会社サンプル（架空の事業者）',
            'City, Country' => '東京都千代田区（所在地の長い表記と建物名の折り返しを確認するための見本です）',
            'Advisory, project delivery and ongoing support' => '事業計画のご相談、業務改善に向けた導入支援、継続的な運用の見直し',
            '[Name and role]' => '［役職・氏名を入力］', '[Year]' => '［設立年を入力］',
            '[Public business contact details]' => '［公開してよい業務用連絡先を入力］',
            'Start a conversation' => 'まずは、お話をお聞かせください',
            'Tell us what you are working on and where you would like to go. Together, we can explore a practical next step.' => 'まだ具体的になっていない課題でも構いません。現在の状況と、これから実現したいことをお聞かせください。',
            '[Add your public business contact details or a link to your Contact page.]' => '［公開用の連絡先、またはお問い合わせページへのリンクを設定してください］',
        ];
        if (isset($labels[$text])) { return $labels[$text]; }
        if (str_contains($context, 'description')) {
            return 'お客様の状況を丁寧に伺い、現場で無理なく続けられる方法を一緒に考えます。計画から実施、その後の振り返りまで、必要な段階に合わせて支援します。';
        }
        return $translated;
    }, 10, 4);
}
wp();
$preview = '';
$page = $_GET['page'] ?? '';
$page_patterns = ['about' => 'page-about', 'services' => 'page-services'];
$slugs = isset($page_patterns[$page]) ? [$page_patterns[$page]] : ['representative-message', 'case-study', 'services', 'process', 'company-information', 'call-to-action'];
if (isset($page_patterns[$page])) {
    $preview = '<h1 class="wp-block-heading">' . esc_html($page === 'about' ? 'About our company' : 'Our services') . '</h1>';
}
foreach ($slugs as $slug) {
    ob_start();
    require get_template_directory() . '/patterns/' . $slug . '.php';
    $preview .= do_blocks(ob_get_clean());
}
?>
<!doctype html><html <?php if (($_GET['language'] ?? '') === 'ja') { echo 'lang="ja"'; } else { language_attributes(); } ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?><div class="wp-site-blocks"><main class="is-layout-constrained"><?php echo $preview; ?></main></div><?php wp_footer(); ?></body></html>
