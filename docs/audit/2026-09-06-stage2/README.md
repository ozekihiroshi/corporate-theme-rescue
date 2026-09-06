# Second-stage audit

No theme runtime files changed in this stage. The ZIP tested remains the artifact
recorded in `../../release-audit.md`. No release, submission, commit or push was
performed in this stage.

## Completed

- Created a dedicated page on 8090: `http://localhost:8090/?page_id=5`.
  It includes long Japanese headings, mixed-language text, an unbroken identifier,
  nested lists, quotation, table, code and details blocks. No existing demo page
  was overwritten. The page is marked `_oc_editor_audit=1`.
- WordPress save / revision restore passed: baseline revision 6, page 5,
  three revisions at the initial server-side check. This used the core revision
  API, not the revision comparison screen.
- Gutenberg parsed all fixture blocks successfully. The editor's data store and
  normal `savePost` pipeline saved a change, reloaded it, and restored the baseline.
  This is not a claim of a complete mouse/keyboard authoring workflow test.
- Playwright Firefox build 1538: 8090 home and fixture page at 1440, 390 and
  320px: six successful page checks; no horizontal page overflow, broken images
  or JavaScript errors. Desktop submenu and mobile menu keyboard behavior passed.
- Inspected `firefox-default-hero.jpg` and computed DOM colors. The cover is
  transparent, its overlay is RGB(23,33,43) at opacity 0.7, the underlying body
  is white, and all hero text is white. The resulting contrast is approximately
  6.04:1. The initial solid-background composition is acceptable; user-selected
  photographs, colors and opacity require their own contrast review.

## Environment limitations

The initial Firefox installation to C: failed with ENOSPC. It was stopped and
successfully repeated using D: for both temporary files and browser binaries.
C: had approximately 125 MiB free at the last measurement; D: had about 626 GiB.
No unrelated files were deleted, moved or cleaned up.

The full Edge/Firefox comparison encountered ERR_INSUFFICIENT_RESOURCES during
the Edge run. That full run is not counted as a pass. A smaller Firefox-only run
completed. No headless Edge process remained at the time of inspection.

Browser downloads are excluded by `/.audit-tools/`; they are not theme assets.
Use TEMP/TMP and PLAYWRIGHT_BROWSERS_PATH pointing there when space on C: is low.
For the reduced run, set OC_BROWSERS=firefox and OC_QUICK=1.

## Still outstanding

- Full Edge/Firefox comparison: now completed successfully in the
  [retry report](../2026-09-06-stage2-retry/README.md); the original failure above
  is retained as historical evidence.
- Actual Safari testing and screen-reader listening/navigation (e.g. NVDA).
- Official Theme Unit Test import and broader authoring flows.
- Japanese/English UI and document-language review.

These results do not certify WCAG conformance or justify an accessibility-ready tag.
