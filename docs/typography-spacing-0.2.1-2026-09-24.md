# Typography and spacing candidate — 0.2.1

Date: 2026-09-24

This checkpoint evaluates design-system rules and a structured visual pass. It
does not replace the maintainer's final review of rhythm, tone or perceived
Japanese type quality, and it is not an update to the submitted 0.2.0 theme.

## Candidate artifact

- File: `build/typography-spacing-2026-09-24/ozeki-corporate-0.2.1.zip`
- SHA-256: `bcd99dc2cb2ae9ee2d57d12521b7e944741d9cdf49f720e9ba5736bba7c63cad`
- Theme branch: `codex/typography-spacing-0.2.1`
- Environment branch: `codex/typography-spacing-0.2.1`

## Design contract

- Shared spacing presets: 4, 8, 16, 24, 40, 64 and 96px.
- WordPress default spacing presets and arbitrary spacing values are disabled.
- Arbitrary font sizes and line heights are disabled.
- International default body line height: 1.75.
- Japanese Refined root line height: 1.85; paragraph line height: 1.9.
- Japanese H1–H3 line heights: 1.35, 1.4 and 1.5.
- Content measure remains 720px.

`check-design-system.php` verifies the raw theme contract, rejects unknown
spacing preset references and rejects raw CSS margin, padding and gap values.

## ZIP matrix

The candidate ZIP was newly installed and checked in all eight combinations:

- PHP 8.1, 8.2, 8.3 and 8.4
- WordPress 6.6 and 7.1

All eight passed. Every run included PHP lint, template and pattern rendering,
the design-system contract, guide links and Theme Check. Theme Check reported no
non-INFO findings; its sole INFO confirmed the `ozeki-corporate` text domain.

Raw matrix logs remain in `/tmp/oc-matrix-results.KF0Nz2a3` for this local run.

## Computed browser measurements

`check-design-metrics.cjs` tested Edge, Firefox and WebKit at 1440, 768, 390
and 320px widths.

Default style, two pages (24 measurements):

- failures: 0
- body line-height ratio: 1.75 (allowing browser rounding)
- visible paragraph ratios: 1.70–1.90
- visible H1–H3 ratios: 1.20–1.50
- content width: 720px maximum, then 342px and 272px at narrow widths
- horizontal overflow: 0px

Japanese Refined, one mixed Japanese/English page (12 measurements):

- failures: 0
- root line-height ratio: 1.85
- visible paragraph ratio: 1.90
- visible H1–H3 ratios: 1.35–1.50
- horizontal overflow: 0px

Machine-readable evidence is stored in ignored local directories:

- `.audit-tools/typography-spacing-2026-09-24/`
- `.audit-tools/typography-spacing-japanese-2026-09-24/`

Japanese Refined was applied through a query-scoped, test-only filter on the
isolated localhost:8090 site. It did not update the database. The temporary
mu-plugin copy was removed after the measurements.

## Structured visual review

The installed ZIP on localhost:8090 was captured at 1440x1000 and 390x844.
Viewport and full-page PNGs are stored in the ignored local directory
`.audit-tools/typography-spacing-visual-2026-09-24/`.

- The English starter uses the bundled front-page template and real bundled
  patterns. Heading hierarchy, three-card rhythm and transitions between 40,
  64 and 96px spacing levels remain distinct without looking disconnected.
- At 390px the hero becomes one column, cards stack with consistent internal
  spacing, the company table stays inside the viewport and the call-to-action
  remains visually separate from the footer.
- The Japanese Refined regression page exercises a long Japanese heading,
  mixed Japanese/English prose, nested lists, a quote, a table and long tokens.
  Mincho body text remains open at 1.9, headings remain compact enough to read as
  headings, and the 390px table and long tokens do not create horizontal scroll.
- No critical clipping, collision, orphaned control or inconsistent section
  rhythm was found in this pass. No additional design-value change was made
  after the captures.

The Japanese capture is deliberately a mixed-content typography fixture, not a
claim that the theme imports a Japanese demo site. A final maintainer comparison
on real Japanese company copy remains the next subjective approval gate.

The temporary English fixture and query-scoped Japanese style filter were both
removed from the 8090 container after capture. The WordPress database was not
changed.
