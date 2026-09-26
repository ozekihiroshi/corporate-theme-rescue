# Pattern system development — 2026-09-26

Branch: `codex/corporate-pattern-system`. Submitted 0.2.3 ZIP unchanged.

Implemented for development:
- Shared section spacing and inherited item typography.
- Standalone process pattern reused by the Services page.
- Section category for services, company information, contact and process.
- Company information expanded to six rows with explicit row headers.
- Services introductory text and explicit contact setup placeholder.
- Guide updated to explain replacing the contact placeholder.

Static contract check passes for eight sections and six company rows.
This is NOT editor round-trip approval or a release candidate.
8090 initially refused connections; Docker subsequently recovered. The existing
ziptest WordPress and DB were started without adding ports. A development ZIP
was installed from `build/pattern-development`, separate from the submission ZIP.
All pattern PHP files passed lint. Browser inspection of the homepage and the
four-section local fixture confirmed stacked service cards, process steps,
wrapping company information and the contact callout at the current narrow
browser width. Follow-up checks covered English and long Japanese fixture text
with Japanese Refined at 1440, 390 and 320px. No document horizontal overflow:
scroll widths 1425, 375 and 305px respectively (scrollbar excluded).
All four sections use 64px vertical padding on desktop and 40px on mobile.
The preview now renders blocks before wp_head so on-demand block CSS is loaded.
The fixture at http://localhost:8090/oc-pattern-preview.php is localhost-only
and creates no posts or settings. Source: `scripts/pattern-preview.php`.
Japanese fixture: append `?language=ja`. This is test text, not a shipped translation.

WP-CLI POT generation passed. PHP block parsing/serialization passed for starters.
`check-pattern-persistence.php` passed WordPress insert/update/cache-clear/reload
and rendering with a Japanese company-name edit. Owned draft 1837 was moved to
Trash (recoverable). This is backend persistence, not a browser editor test.

Remaining before deployment/commit:
- Browser editor insertion/save/reopen and full runtime regressions.
- Empty/short content and replacement-image cases.
- Complete service visual variants and configured contact-link workflow.

No AWS update, submission, commit or push was performed in this step.

## Representative message and case study follow-up

Added `representative-message` (40/60 photo and message) and `case-study`
(50/50 photo and overview followed by situation, approach and outcome).
Both reuse the shared section rhythm, inherited typography and existing story
image styling. Existing bundled illustrative photography is explicitly labelled;
users must replace names, photographs and project details. The case study is
optional and contains no invented results or endorsements.

The getting-started guide explains insertion, replacement and suggested placement.
The local preview now includes both sections before the previous four sections.
English is the source language; Japanese preview text is a testing fixture.

Validation: new PHP files linted, POT regenerated, ten-section static contract
passed, and all twelve checked section/page patterns passed WordPress block
parsing and serialization. Development ZIP installed on the existing 8090 site.
English and Japanese desktop screenshots and Japanese 320px screenshot inspected;
the two new sections stack and wrap without overlapping. English 390px document
width check also passed. This does not establish editor round-trip approval for
the two new patterns, replacement-image coverage, or comprehensive accessibility.

Submitted 0.2.3 ZIP, AWS, existing saved pages and review ticket were not changed.
