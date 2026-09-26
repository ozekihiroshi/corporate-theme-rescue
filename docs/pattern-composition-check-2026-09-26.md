# Pattern editing and composition check — 2026-09-26

Local 8090 only. No AWS, submission, commit or push.

## Browser editor check

- Created owned draft 1843, `代表挨拶・事例 編集検証（非公開）`.
- Inserted representative-message and case-study from the normal Patterns menu.
- The in-app visual editor canvas was blank (same limitation seen previously).
  Therefore image/text changes were made through the visible Code editor, NOT
  the visual image replacement control. Do not count that control as tested.
- Changed two headings, representative name, both image URLs and then alt text,
  captions and case-study text. Save, reload, further edit, save and reload passed.
- Front-end authenticated draft preview contains the changed text and both loaded
  test images: vertical 300x580 and horizontal 580x300, rendered at 4:3 by story CSS.
- WordPress confirms status `draft`; nothing was published. Draft retained for
  manual Chrome comparison. Existing draft 1839 was not changed.
- First editor load showed a WordPress.org secure-connection/update-check warning;
  reload succeeded. No update or external-connection configuration was changed.

## Page starter composition

- About: introduction, strengths, representative message, company information,
  contact guidance. One photograph, no repeated introduction photograph.
- Services: services, process, optional case study, contact guidance.
- Existing saved pages remain untouched. These changes affect new insertions.
- Guide explains one-time insertion into an empty page and removing optional parts.
- Preview supports allowlisted `?page=about` and `?page=services` only.
- English About desktop screenshot and Services mobile screenshot inspected.
  About at 390px had document scroll width 375px (scrollbar excluded).
- Static ten-section contract and twelve WordPress parse/serialize checks passed.
  POT regenerated and development ZIP installed, separate from submitted 0.2.3.

Remaining: normal visual image replacement/editor validation in Chrome, new
page-starter insertion/save/reopen in the visual editor, and full release regression.
This is development verification, not release approval.

## Page starter browser follow-up

- Inserted About and Services through the new-page starter selection dialog.
- Owned About draft 1846: changed company name, saved, reloaded; then changed
  representative field and saved again. Authenticated front-end preview confirmed
  both values and the five expected section headings.
- Owned Services draft 1848: changed a service heading, saved and reloaded; changed
  another heading and saved again. Preview confirmed the second edit, all four
  section headings and loaded image.
- Content edits used the visible Code editor. Normal visual canvas remained blank
  in the available in-app browser; no normal image Replace control test passed.
- Drafts 1846 and 1848 are retained for human Chrome checks. No publish action was
  taken, and no existing user pages, AWS, submitted ZIP or theme source changed.

Manual remainder: in Chrome open draft 1843, switch to Visual editor if needed,
select each photograph, use Replace > Media Library, select a different test image,
save the draft, reopen and verify the selected image and its alternative text.
Do not publish these test pages.

## User-confirmed photo replacement

The user replaced both photographs by drag-and-drop in Chrome, supplied screenshots
at 12:13 and 12:19, and explicitly confirmed saving and reloading with the selected
images retained. This verifies that workflow, not the untested Replace-menu route.
The saved target is draft 1843 (the ambient browser's 1848 URL is a different page).
Read-back confirms attachment 1854 representative-sample-1.png and attachment 1856
case-study-workspace-1.png. Both image alt attributes were empty after replacement;
the old vertical/horizontal test captions remained. Scoped cleanup script
`scripts/finish-photo-fixture.php` sets descriptive Japanese alt text and explicit
AI-example captions, preserving URLs, layout, other text and draft status.

Earlier "manual remainder" entries above describe the state before this user test.
Still pending: complete visual editing of composed starters, full release regression
and comprehensive accessibility assessment. User-confirmed replacement is not a
claim of overall accessibility conformance.

Cleanup execution: `PHOTO_DESCRIPTIONS_OK draft=1843 images=1854,1856`.
Authenticated browser read-back confirmed both alt strings, captions and loaded
images. Full-page 390px screenshot inspected: photos stack above their messages,
captions wrap, and the three case-study items stack vertically.
Services draft 1848 at 390px: one main H1, expected four H2 sections, no broken
content images, no empty/hash-only content links, document scroll width 375px.
Its contact section is still an explicit setup placeholder, NOT a working contact
destination. This and bracketed project information must be configured before a
real business site is published. No invented contact destination was added.
The getting-started guide now explains checking alt text and captions after image
replacement. Shared-pattern source contract and git whitespace checks pass.

## Guide and publishing checklist finish

Updated the theme's read-only admin guide and English/Japanese getting-started
document with page-starter choices, About/Company overlap, optional case studies,
one-time insertion versus individual sections, permissions/evidence and image
replacement checks. The Markdown guide has a seven-item checklist in each language.
No automatic checks or certification are claimed. PHP lint and whitespace checks
passed; POT regenerated. Copied the guide PHP, Markdown and POT to existing 8090
for development inspection (no new ZIP built in this step). Authenticated admin
guide rendered its six headings and the new English/Japanese guidance correctly.
AWS, submitted ZIP, saved pages and Git remotes remain unchanged by this guide step.
