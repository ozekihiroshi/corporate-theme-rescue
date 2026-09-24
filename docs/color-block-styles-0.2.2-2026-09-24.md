# Color and block-style candidate — 0.2.2

Date: 2026-09-24

This checkpoint builds on the accepted 0.2.1 typography/spacing candidate. It
keeps the default composition quiet while adding a clearer corporate color
hierarchy and two opt-in block styles. It is not an update to the submitted
0.2.0 theme.

## Candidate artifact

- File: `build/pre-publication-0.2.2-2026-09-24/ozeki-corporate-0.2.2.zip`
- SHA-256: `c9229dc4e70cbaa6675b114ae607557c644b829161f5ca6093e70df39108c2ef`
- Theme branch: `codex/color-block-styles-0.2.2`
- Environment branch: `codex/color-block-styles-0.2.2`

The exact ZIP was force-installed and activated on the isolated localhost:8090
site. WP-CLI reports version 0.2.2. The site has no theme-source bind mount.

## Color contract

The public editor palette contains eight intentional roles:

- White `#ffffff`
- Ink `#17212b`
- Navy `#183b56`
- Slate `#5b6670`
- Mist `#f3f5f6`
- Line `#d6dde2`
- Deep Teal `#087f73`
- Deep Teal Dark `#075e56`

Navy is used for primary buttons and the large call-to-action surface. Ink
remains the main text color. Deep teal is retained for links, short labels,
numbers, focus outlines and the narrow Key Point rule. The saved preset slugs
from earlier versions remain unchanged; only one new `navy` slug is added.

Contrast against White is 16.29:1 for Ink, 11.67:1 for Navy, 5.87:1 for Slate,
4.89:1 for Deep Teal and 7.65:1 for Deep Teal Dark. Each text-capable role meets
WCAG AA's 4.5:1 normal-text threshold.

## Optional block styles

- Image / Soft Shadow: `0 12px 30px` Ink at 12% opacity. It affects the image,
  not the caption, and falls back to a visible border in forced-colors mode.
- Group / Key Point: Mist background, 4px Deep Teal logical-start border, Ink
  text and 24px padding. `border-inline-start` supports writing direction.

Both styles are registered with WordPress on `init`; the shared `style.css` is
loaded on the public site and in the editor. The English/Japanese guide explains
where to find them and recommends sparing use.

## Automated results

`check-design-system.php` validates the exact palette, editor-facing names,
contrast, shared spacing/typography contract, style registration, selectors and
logical border. It passed against the source mount and the ZIP installed on 8090.

Browser-computed checks at 1440, 390 and 320px all passed:

- failures: 0
- horizontal overflow: 0
- Key Point background: `rgb(243, 245, 246)`
- Key Point border: `4px rgb(8, 127, 115)`
- Key Point padding: `24px`
- Soft Shadow: `rgba(23, 33, 43, 0.12) 0px 12px 30px`
- primary button: Navy with White text

The exact candidate ZIP passed all eight PHP/WordPress combinations:

- PHP 8.1, 8.2, 8.3 and 8.4
- WordPress 6.6 and 7.1

Every combination included PHP lint, pattern/template parsing and serialization,
the design-system contract, guide checks and Theme Check. Theme Check reported
no non-INFO findings; its one INFO confirmed the `ozeki-corporate` text domain.
Raw logs remain in `/tmp/oc-matrix-results.sgliwjWr` for this local run. Two
WordPress 7.1 downloads ended early during that run before theme installation;
PHP 8.2 / WordPress 7.1 and PHP 8.3 / WordPress 7.1 were rerun individually and
both completed all checks successfully. The transient audit database was stopped
after testing.

## Visual evidence and boundaries

Desktop/mobile captures from the runtime-identical candidate are stored in the ignored local directory
`.audit-tools/color-block-styles-0.2.2-2026-09-24/`. The structured pass found:

- the full-width Navy call to action provides the main color anchor;
- teal remains limited to small accents and does not dominate the page;
- Soft Shadow separates the photograph without creating a floating card effect;
- Key Point is distinct at desktop and especially clear at phone widths;
- the existing English starter and Japanese Refined typography retain their
  established spacing and hierarchy.

The final ZIP differs from the captured runtime only in README/readme text; it
was installed on 8090 and passed the same design-system contract. The screenshot
and measurement fixtures were localhost-only, made no database changes and were
removed from 8090 after use. The maintainer completed the human visual review on
8090 and accepted the color and block-style presentation. This report closes the
pre-publication validation checkpoint; tagging, GitHub Release creation, AWS
deployment and WordPress.org submission remain separate publication actions.
