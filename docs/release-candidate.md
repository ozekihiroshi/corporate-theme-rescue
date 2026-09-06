# Ozeki Corporate 0.1.0 release candidate

Historical preparation checkpoint. The artifact and pending items below describe
the pre-interaction candidate, not the final release. See
[final interaction checks](audit/2026-09-06-interactions/README.md) and the theme
repository's docs/release-0.1.0.md for the subsequently fixed artifact.

## Artifact and source

- Theme commit: 7e1feb9343d0ebc3e528b6a63ce23d9ede7f4c54
- Environment evidence checkpoint: 8c96ced7a99d3d95c46743154f1cd2802d5c07f4
- Artifact: ozeki-corporate/build/ozeki-corporate-0.1.0.zip
- SHA256: 985fe70bb5b96b0fbe811d26b9b4c51d189ef400be5d14bae84dc88be43430c6
- All 24 ZIP files matched current source, allowing the build's CRLF-to-LF
  normalization. No docs, Git data or node_modules were present in the ZIP.
- Required block-theme files present: style.css, readme.txt, theme.json,
  templates/index.html. LICENSE and screenshot.png are bundled.
- Readme identifies GPL licensing, resource authorship/source, AI-generated demo
  photography, system fonts, no CDN/runtime dependencies, and no automatic demo import.

## Draft GitHub Release title

Ozeki Corporate 0.1.0

## Draft description

Ozeki Corporate is a lightweight corporate block theme built with WordPress core
blocks, templates, patterns and Global Styles. It includes an optional Japanese
Refined style variation with system-font typography for Japanese and mixed-language
content. No page builder or companion plugin is required.

Requirements: WordPress 6.6 or later; PHP 8.1 or later.
License: GPL-2.0-or-later.

The separately maintained sample site includes fictional company text and generated
photographs; installing the theme does not import that content.

Verification includes Theme Check, a representative PHP/WordPress smoke matrix,
Edge/Firefox checks and Edge/WebKit Theme Unit Test layout scans. The latest fixed
ZIP passed 88 recorded layout cases. The user reported basic iPhone Safari and
VoiceOver operation; this is not a comprehensive accessibility certification.

Download the explicitly attached theme ZIP, not GitHub's automatically generated
source archive. Attach only the verified artifact above unless it is rebuilt and
the checksum and evidence are updated.

## Remaining before final release decision

- Exercise actual Theme Unit Test pagination, password form and comment reply/
  threading interactions; existing page scans do not establish these behaviors.
- Complete broader editor and Japanese/English document-language review.
- Mac Safari is unavailable; record the limitation rather than claiming it passed.
- Screen-reader usability beyond basic operation remains unevaluated; do not add
  accessibility-ready or claim full WCAG conformance.
- readme changelog currently says Initial development release. Decide final release
  wording before tagging; any ZIP change requires rebuilt-artifact verification.
- Once approved: commit documentation changes, tag the final source commit, create
  GitHub Release with the verified ZIP, then prepare the WordPress.org theme upload.

Official requirements checked:
https://make.wordpress.org/themes/handbook/review/required/

This checklist is not WordPress.org approval or a complete manual theme review.
