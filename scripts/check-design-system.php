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

$expected_palette = [
	'base'        => '#ffffff',
	'contrast'    => '#17212b',
	'navy'        => '#183b56',
	'muted'       => '#5b6670',
	'surface'     => '#f3f5f6',
	'border'      => '#d6dde2',
	'accent'      => '#087f73',
	'accent-dark' => '#075e56',
];
$expected_palette_names = [
	'base'        => 'White',
	'contrast'    => 'Ink',
	'navy'        => 'Navy',
	'muted'       => 'Slate',
	'surface'     => 'Mist',
	'border'      => 'Line',
	'accent'      => 'Deep Teal',
	'accent-dark' => 'Deep Teal Dark',
];
$actual_palette = [];
$actual_palette_names = [];
foreach ($base['settings']['color']['palette'] ?? [] as $preset) {
	$actual_palette[(string) $preset['slug']] = strtolower((string) $preset['color']);
	$actual_palette_names[(string) $preset['slug']] = (string) $preset['name'];
}
$assert_same($expected_palette, $actual_palette, 'Color palette');
$assert_same($expected_palette_names, $actual_palette_names, 'Color palette names');
$assert_same(
	'var(--wp--preset--color--navy)',
	$base['styles']['elements']['button']['color']['background'] ?? null,
	'Button background'
);

$luminance = static function (string $hex): float {
	$channels = str_split(ltrim($hex, '#'), 2);
	$linear = array_map(static function (string $channel): float {
		$value = hexdec($channel) / 255;
		return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
	}, $channels);
	return (0.2126 * $linear[0]) + (0.7152 * $linear[1]) + (0.0722 * $linear[2]);
};
$contrast_ratio = static function (string $first, string $second) use ($luminance): float {
	$light = max($luminance($first), $luminance($second));
	$dark = min($luminance($first), $luminance($second));
	return ($light + 0.05) / ($dark + 0.05);
};
foreach (['contrast', 'navy', 'muted', 'accent', 'accent-dark'] as $foreground) {
	if ($contrast_ratio($expected_palette[$foreground], $expected_palette['base']) < 4.5) {
		throw new RuntimeException('Insufficient contrast for ' . $foreground . ' on base.');
	}
}

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

foreach (['.wp-block-image.is-style-soft-shadow img', '.wp-block-group.is-style-key-point'] as $selector) {
	if (! str_contains($css, $selector)) {
		throw new RuntimeException('Missing block-style CSS: ' . $selector);
	}
}
if (! str_contains($css, 'border-inline-start:')) {
	throw new RuntimeException('Key Point must use a direction-aware border.');
}

$styles = WP_Block_Styles_Registry::get_instance();
if (! $styles->is_registered('core/image', 'soft-shadow')) {
	throw new RuntimeException('Soft Shadow is not registered for core/image.');
}
if (! $styles->is_registered('core/group', 'key-point')) {
	throw new RuntimeException('Key Point is not registered for core/group.');
}

echo 'DESIGN_SYSTEM_OK palette=8 contrast>=4.5 spacing=4,8,16,24,40,64,96 body=1.75 japanese=1.9 styles=soft-shadow,key-point' . PHP_EOL;
