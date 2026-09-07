# Starter follow-up: company table and setup guidance

## Changes

- Theme table styles now apply to template and pattern tables as well as tables
  inside post content. Header cells retain a 6em minimum width; cell padding is
  responsive. Native table semantics are unchanged. Long values can wrap.
- English and Japanese GETTING-STARTED.md is included in the ZIP and linked from
  the repository README. readme.txt also covers contact linking and image limits.
- Contact instructions distinguish a page/button link, an email-app link and a
  separately configured form plugin. No invented address or automatic form was
  added. Actual mail submission/delivery is not claimed as tested.
- Image instructions cover the hosting-dependent limit, the test server's 2MB
  example, processing completion, alternative text, original preservation and
  the hero's 5:4 crop. Server limits were not modified.

## Verification

- Development ZIP SHA-256:
  8070b6b3a4ca802c54ece6cec10e3e8891d3e536ed2bd68c7f5256638e2b24d5
- Output: .audit-tools/starter-release-followup/ozeki-corporate-0.1.0.zip
- This is NOT the published 0.1.0 artifact; version still pending finalization.
- ZIP presence of GETTING-STARTED.md and readme.txt checked.
- Extracted only to the existing isolated 8091 theme directory as UID/GID 33:33.
  No database write, template reset or source mount was used for deployment.
- Edge widths 1440, 390 and 320: Company, Location and Business each one line;
  document/table widths did not overflow. 320px table screenshot visually checked.
- Same widths with Japanese row labels, mixed-language text and a long unbroken
  value: no overflow. Stress content was inserted only into the live DOM, not DB.
- Evidence: .audit-tools/starter-table-followup/table-results.json and JPGs.
- Test: scripts/check-starter-table.cjs.
- git diff --check passed.

The prior edited hero copy and uploaded photograph are retained on 8091. Existing
8089/8090, AWS, GitHub releases and tags were not changed. No commit/push performed.
This is not a new Mac Safari, full accessibility or end-to-end contact-form test.

The older review's table-wrap issue is addressed. Its contact and upload-help
gaps are addressed by the shipped guide, not by automatically configuring a
contact destination. Supporting-page patterns/navigation onboarding remain a
separate product-work item.
