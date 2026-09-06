# Interactive release checks

Target: existing isolated ZIP installation on localhost:8090 only.
No AWS or 8089 content/settings changes in this test.

The classic Theme Unit Test pagination post initially lacked page-number links.
Core post-content in installed WordPress only added them when has_block(nextpage)
was true. A theme render filter supplies core wp_link_pages output for classic
markers on the queried singular post, excluding protected content and block-based
pagination to avoid duplicates. The changelog was finalized before ZIP rebuilding.

Final ZIP SHA256:
70a3e132178a57b6a9c642a8134543202603cf2008c160ebb06b035eea7dc4a7

## Observed results

- Final theme commit: bdd1c7e. ZIP's 24 files match that source (normalized text).
- Final PHP 8.1/8.2/8.3/8.4 x WordPress 6.6/7.1: all eight smoke/runtime/Theme
  Check cases passed. Raw logs in matrix/. This is representative, not every
  intermediate WordPress release or full interactive matrix coverage.
- Final Edge/WebKit 88-case layout rerun: zero findings; raw logs in layout/.

- Post 1171: click page 2, observe Post Page 2; click page 1, observe Post Page 1.
- Post 1168: wrong password retains form; correct fixture password removes form;
  clearing cookies restores password protection.
- Post 1148: Reply sets parent; Cancel resets parent; reply submission visible
  as pending moderation. DB independently confirmed comment 35, post 1148,
  parent 8, approval 0. This test comment remains as evidence, not auto-approved.
- Theme Check 20260901: one INFO for correct text domain, no REQUIRED/WARNING.
- PHP syntax passed. See interactions.json for browser observations; temporary
  moderation token omitted from the recorded redirect.

These tests do not cover every comment threading/pagination configuration or
replace the pending full manual, Mac Safari and screen-reader usability reviews.
