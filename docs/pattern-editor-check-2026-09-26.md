# Pattern editor check — 2026-09-26

Target: existing localhost:8090, in-app browser, authenticated local administrator.
No existing page content was modified and no page was published.

Created draft 1839, title `パターン編集検証・非公開下書き`.

Confirmed through normal Gutenberg UI:
- Inserted Services page starter (services and process sections).
- Ozeki Corporate sections category lists all four development sections.
- Inserted Company information and Contact call to action after the starter.
- Edited service heading, company table value and contact placeholder into Japanese.
- Save draft completed; page list identifies the page as Draft.
- On reopening, Code editor shows all four sections and all three Japanese edits.
  Table row header scope attributes are preserved.

Unresolved visual-editor issue:
- Reload and reopening from the page list leave the editor canvas at about:blank.
- Code editor remains usable and content is present.
- Switching back to Visual editor and opening a new tab did not resolve it.
- Existing Sample Page (ID 2), opened read-only as a control, also has the blank
  canvas. This is not evidence of a fault specific to the new patterns.
- Browser log contains a MutationObserver.observe non-Node error; its source
  has not been attributed, so no theme workaround was applied.

Keep draft 1839 for comparison in the user's ordinary Chrome browser. Do not
claim visual editor reopen passed. Next: compare Chrome and investigate editor
or browser integration if the issue remains. Draft can later be moved to Trash.
