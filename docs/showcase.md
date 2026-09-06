# 8089 corporate showcase

After the initial `seed-content.php` fixture, activate Ozeki Corporate and run:

```sh
docker compose run --rm wp-cli wp theme activate ozeki-corporate
docker compose run --rm wp-cli wp eval-file /workspace/scripts/seed-showcase.php
```

The script defaults to requiring `http://localhost:8089` and the active
`ozeki-corporate` theme. Remote use requires an explicit `OC_SHOWCASE_TARGET`
matching the site's home URL; see [AWS deployment record](aws-showcase.md).
Do not rerun the site-specific AWS deployment script casually: it replaces demo
content and global styles. Obtain a fresh backup and review its approved page ID.
It refuses to overwrite any target without `_ozeki_corporate_fixture=1`.
The first pre-update fixture state is retained in the non-autoloaded option
`ozeki_corporate_before_showcase`; normal content revisions also remain.
Images are reused by their `_oc_showcase_photo` marker on subsequent runs.
Running again replaces fixture-owned sample text, so do not rerun after editing
the sample unless that replacement is intended. No posts are deleted.

## Pages

- `/`: editable home with hero, services, approach, workflow, model case,
  news cards, FAQ and contact CTA.
- `/about/`, `/services/`, `/company/`, `/contact/`: realistic Japanese copy.
- `/english/`: English corporate sample with Latin-first system typography.
- `/design-guide/`: headings, mixed-language paragraphs, photos, cards, table,
  quote, details, button, search, and links to archive/search/404.
- `/news/`: the normal post archive; three example articles have thumbnails.

All content describes a fictional company. Photos are AI-generated. The contact
page deliberately has no form submission or live mail action.

## Theme and environment boundary

The theme contains reusable `oc-*` composition styles. Fictional copy, original
images, attachment IDs and navigation belong to this environment only.
Fixture-owned `wp_template` and `wp_template_part` records replace front-page,
header, footer and news archive on 8089. The front-page renders the Home page's normal block
content, so it can be edited in Pages. The theme's default templates are retained.
Existing Japanese Refined global styles are not reset by the fixture.

## Visual acceptance

Check desktop, tablet and mobile; use a real responsive viewport, not a cropped
desktop screenshot. Confirm no horizontal overflow, all local images load,
headings wrap, service cards stack, navigation opens/closes by keyboard, and
the English and Japanese pages remain legible. Check the block editor for
invalid blocks before saving a sample page.
