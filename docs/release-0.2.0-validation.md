# 0.2.0 candidate validation — 2026-09-07

## Final artifact

- `ozeki-corporate/build/ozeki-corporate-0.2.0.zip`, 877,758 bytes.
- SHA-256: `694b832d8469057becb60ac835554f330456ff6ceca0d0b9049ad31727960c3f`.
- All 32 ZIP files match theme source (CRLF normalization allowed for text).
- No development scripts/docs, Git files or node_modules in the distribution.
- Header/readme version 0.2.0; WordPress >=6.6; PHP >=8.1; tested header 7.1.
- English 1200 x 900 screenshot; full GPL-2 text plus GPL-2.0-or-later notice;
  local image attribution and English/Japanese setup instructions present.
- GPL text compared byte-for-byte with `/usr/share/common-licenses/GPL-2` after
  the theme's license notice. No new licensing terms were introduced.

## Matrix: 8 / 8 passed

| PHP | WordPress 6.6 | WordPress 7.1 |
| --- | --- | --- |
| 8.1 | PASS | PASS |
| 8.2 | PASS | PASS |
| 8.3 | PASS | PASS |
| 8.4 | PASS | PASS |

Each run installs WordPress into a new temporary directory/database prefix,
installs and activates the same final ZIP, lints PHP, renders templates/patterns,
checks starter registration/serialization and runs Theme Check 20260901.
All eight have no non-INFO findings. The single INFO concerns the correct sole
text domain, ozeki-corporate. Exit status of the full matrix: 0.

Raw evidence: [matrix logs](audit/2026-09-07-release-0.2.0/summary.txt), adjacent
per-combination logs, artifact hash and exit-code.txt. Temporary run directory:
`/tmp/oc-matrix-results.uaptYnIH`. The dedicated audit DB was stopped afterward;
this does not stop 8089, 8090, 8091 or any AWS site.

## Findings fixed before finalization

The previous interrupted attempt stopped after DB startup and provided too little
evidence to identify its exact cause. Current DB health, images and disk were
healthy. Runner now checks DB readiness separately with a 120-second bound,
uses --no-deps for each case, bounds each case to 600 seconds, and immediately
prints the result directory. Summary/exit status persist outside the tool session.

Theme Check's command can exit 0 with WARNING findings. A new JSON gate rejects
every non-INFO finding; WARNING and INFO fixture inputs verify both branches.
Earlier intermediate candidates warned about the original 1.9 MB photograph
and a 677 KB optimization. Neither is a final pass. The final image is a
1200 x 675 palette PNG, 366,984 bytes, visually checked, preserving its URL.
The warning threshold in the installed Theme Check is 500 KiB.

The original LICENSE was a short notice, although readme promised full text.
The final ZIP now contains the complete standard license text as well.

## 8091 final-ZIP UI checks

Installed the exact final ZIP into the independent no-source-mount 8091 site.
Native guide link, Japanese guide anchor, four-pattern chooser, About selection,
title editing, draft saving and reopening pass. Final test draft ID: 17.
All four new patterns parse without invalid blocks. No page was published.
Table checks pass at 1440, 390 and 320 pixels, including Japanese labels and a
long unbroken mixed-language value. No horizontal page/table overflow detected.
Stress text is DOM-only; existing saved homepage content was not rewritten.

Evidence: `.audit-tools/final-0.2.0-694b832/` (screenshots, pattern-validation.json,
saved-draft.json, table-results.json). Screenshots from the earlier guide/design
checks and the final optimized photograph were visually reviewed.

## Scope and next step

These are representative minimum/latest WordPress smoke tests, not every minor
WordPress version or complete feature/browser coverage. About has an end-to-end
saved-page test; the other starters have structural/block checks. Navigation and
contact guidance was reviewed, not a live form delivery test. New Safari,
VoiceOver and comprehensive accessibility approval are not claimed.

No commit/push, tag, Release, WordPress.org submission or AWS update was performed
in this checkpoint. AWS retains the previously deployed development version.
Next: review these changes, commit/push, then create a new v0.2.0 Release with this
exact artifact if authorized. Do not overwrite the existing v0.1.0 release.
