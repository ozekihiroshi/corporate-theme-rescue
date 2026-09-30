# Ozeki Corporate 0.3.0 release checkpoint

## Artifact

- Installation ZIP: `../ozeki-corporate/build/release-0.3.0/ozeki-corporate-0.3.0.zip`
- SHA-256: `8b494ed5ebe7f67860815013245e302ae652faaea55ef2e4b845f112237f8252`
- The original submitted 0.2.3 ZIP is preserved separately in `build/review-0.2.3`.
- Changes: shared section rules, representative message, case study, process,
  composed About/Services starters and English/Japanese publishing guidance.
- Existing saved content, navigation, templates and Global Styles are not replaced.

## Validation

- Actual ZIP passed PHP 8.1, 8.2, 8.3 and 8.4 with WordPress 6.6 and 7.1
  (eight isolated installations). Theme Check: no non-INFO findings in all eight.
- Runtime design, starter patterns, guide translations, page comments and
  template translation checks passed in the matrix.
- ZIP integrity and source whitespace checks passed; ten-section contract passed.
- Existing local 8090 installation updated to 0.3.0 from this exact ZIP.
- Matrix logs: `/tmp/oc-matrix-results.vRUzSL79` in WSL (ephemeral).
- Edge browser regression passed automatic/custom three-level menus at 1440,
  390 and 320 CSS pixels: hover, keyboard traversal, mobile Escape/focus return,
  links, comments/replies, protected/closed pages and horizontal overflow.
  Evidence: `output/release-0.3.0/results.json` and screenshots (local).
  Initial test lacked its temporary fixture; after fixture preparation all six
  cases passed. No new Safari/VoiceOver certification claimed.

## External destinations

- GitHub Release published: https://github.com/ozekihiroshi/ozeki-corporate/releases/tag/v0.3.0
  Source commit `4f9ce96`, annotated tag `v0.3.0`, main and tag pushed.
  Re-downloaded release asset matches the tested ZIP byte-for-byte and by SHA-256.
- AWS `https://wp.ceri.link` unchanged: SSH port 22 timed out from WSL and
  Windows. No firewall, SSH, server configuration or content changes attempted.
- WordPress.org update pending. User is logged in through Chrome; the available
  automated browser has a separate unauthenticated session.

The five temporary review pages/comments and the localhost review endpoint
created for this run were removed afterward; existing editing drafts remain.

## Proposed WordPress.org update comment

Hello Themes Team,

Version 0.3.0 builds on the review corrections in 0.2.3. It adds editable
representative-message, case-study and process sections, consistent page starter
compositions, and clearer English/Japanese setup and pre-publication guidance.
The page comments, native hierarchical navigation and translation corrections
from 0.2.3 are retained. Saved user content is not replaced by this update.

The installation ZIP passed our PHP 8.1–8.4 / WordPress 6.6 and 7.1 matrix,
including Theme Check with no non-INFO findings. These checks are not a
comprehensive accessibility certification.

Thank you for your review.
Hiroshi Ozeki
