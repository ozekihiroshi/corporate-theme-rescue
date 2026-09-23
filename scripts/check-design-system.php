<?php
/** Verify the typography and spacing contract without changing site content. */
if (! defined('ABSPATH')) {
	throw new RuntimeException('WordPress must be loaded.');
}

$theme_dir = get_stylesheet_directory();
$read_json = static function (string $file): array {
	$data = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
	if (! is_array($data)) {
		throw new RuntimeException('Expected a JSON object: ' . $file);
	}
	return $data;
};
$assert_same = static function ($expected, $actual, string $label): void {
	if ($expected !== $actual) {
		throw new RuntimeException($label . ' differs. Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
	}
};

$base = $read_json($theme_dir . '/theme.json');
$japanese = $read_json($theme_dir . '/styles/japanese-refined.json');

$expected_spacing = [
	'10' => '0.25rem',
	'20' => '0.5rem',
	'30' => '1rem',
	'40' => '1.5rem',
	'50' => '2.5rem',
	'60' => '4rem',
	'70' => '6rem',
];
$actual_spacing = [];
foreach ($base['settings']['spacing']['spacingSizes'] ?? [] as $preset) {
	$actual_spacing[(string) $preset['slug']] = (string) $preset['size'];
}
$assert_same($expected_spacing, $actual_spacing, 'Spacing scale');

$spacing_settings = $base['settings']['spacing'] ?? [];
foreach (['blockGap', 'margin', 'padding'] as $enabled) {
	$assert_same(true, $spacing_settings[$enabled] ?? null, 'Spacing setting ' . $enabled);
}
foreach (['customSpacingSize', 'defaultSpacingSizes'] as $disabled) {
	$assert_same(false, $spacing_settings[$disabled] ?? null, 'Spacing setting ' . $disabled);
}
$assert_same(['px', 'rem'], $spacing_settings['units'] ?? null, 'Spacing units');

$typography_settings = $base['settings']['typography'] ?? [];
$assert_same(false, $typography_settings['customFontSize'] ?? null, 'Custom font sizes');
$assert_same(false, $typography_settings['defaultFontSizes'] ?? null, 'Default font sizes');
$assert_same(false, $typography_settings['lineHeight'] ?? null, 'Custom line heights');
$assert_same('1.75', $base['styles']['typography']['lineHeight'] ?? null, 'Default body line height');
$assert_same('1.9', $japanese['styles']['blocks']['core/paragraph']['typography']['lineHeight'] ?? null, 'Japanese paragraph line height');
$assert_same('1.35', $japanese['styles']['elements']['h1']['typography']['lineHeight'] ?? null, 'Japanese H1 line height');
$assert_same('1.4', $japanese['styles']['elements']['h2']['typography']['lineHeight'] ?? null, 'Japanese H2 line height');
$assert_same('1.5', $japanese['styles']['elements']['h3']['typography']['lineHeight'] ?? null, 'Japanese H3 line height');
if (isset($japanese['settings']['spacing'])) {
	throw new RuntimeException('The Japanese style must inherit the shared spacing scale.');
}

$files = [$theme_dir . '/style.css', $theme_dir . '/theme.json'];
foreach (['styles', 'parts', 'patterns', 'templates'] as $directory) {
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme_dir . '/' . $directory));
	foreach ($iterator as $file) {
		if ($file->isFile()) {
			$files[] = $file->getPathname();
		}
	}
}
foreach ($files as $file) {
	$content = (string) file_get_contents($file);
	preg_match_all('/(?:var:preset\|spacing\||--wp--preset--spacing--)([a-z0-9-]+)/', $content, $matches);
	foreach ($matches[1] as $slug) {
		if (! array_key_exists($slug, $expected_spacing)) {
			throw new RuntimeException('Unknown spacing preset ' . $slug . ' in ' . $file);
		}
	}
}

$css = (string) file_get_contents($theme_dir . '/style.css');
preg_match_all('/(?:^|[;{]\s*)(?:margin|padding|gap)(?:-[a-z-]+)?\s*:\s*([^;}]+)/mi', $css, $spacing_declarations);
foreach ($spacing_declarations[1] as $value) {
	$remainder = preg_replace('/var\(--wp--preset--spacing--(?:10|20|30|40|50|60|70)\)/', '', $value);
	$remainder = preg_replace('/(?:\b0\b|\bauto\b|!important|\s)+/', '', (string) $remainder);
	if ($remainder !== '') {
		throw new RuntimeException('Raw spacing value remains in style.css: ' . trim($value));
	}
}

echo 'DESIGN_SYSTEM_OK spacing=4,8,16,24,40,64,96 body=1.75 japanese=1.9' . PHP_EOL;
