<?php
// Temporary local-only block-style fixture. Copy into localhost:8090, capture,
// then remove. It does not write to the database or save template changes.
if (! in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost:8090', '127.0.0.1:8090'], true)) {
	http_response_code(403);
	exit;
}

require __DIR__ . '/wp-load.php';
switch_to_locale('en_US');
add_filter('pre_option_blogname', static fn() => 'Example Studio');

$image = esc_url(get_theme_file_uri('assets/images/team.png'));
$markup = sprintf(
	'<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
	<!-- wp:group {"tagName":"main","align":"full","className":"oc-section","layout":{"type":"constrained"}} -->
	<main class="wp-block-group alignfull oc-section">
	<!-- wp:paragraph {"className":"oc-eyebrow"} --><p class="oc-eyebrow">COLOR &amp; BLOCK STYLES</p><!-- /wp:paragraph -->
	<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">Quiet emphasis for business content</h1><!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">Navy provides structure. Deep teal remains a restrained accent.</p><!-- /wp:paragraph -->
	<!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide">
	<!-- wp:column --><div class="wp-block-column">
	<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-soft-shadow"} --><figure class="wp-block-image size-full is-style-soft-shadow"><img src="%s" alt="Illustration of a team discussing a project" /></figure><!-- /wp:image -->
	</div><!-- /wp:column -->
	<!-- wp:column --><div class="wp-block-column">
	<!-- wp:group {"className":"is-style-key-point","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-key-point">
	<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">A key point</h2><!-- /wp:heading -->
	<!-- wp:paragraph --><p>Use this treatment for a short principle, service promise or important explanation.</p><!-- /wp:paragraph -->
	<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Primary action</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
	</div><!-- /wp:group -->
	</div><!-- /wp:column -->
	</div><!-- /wp:columns -->
	</main><!-- /wp:group -->
	<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->',
	$image
);
$content = do_blocks($markup);
?><!doctype html>
<html lang="en-US"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?>
<div class="wp-site-blocks"><?php echo $content; // Trusted local fixture markup, not request data. ?></div>
<?php wp_footer(); ?></body></html>
