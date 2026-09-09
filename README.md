# Corporate Theme Rescue

Development, distribution-ZIP and first-user verification environments for
[Ozeki Corporate](https://github.com/ozekihiroshi/ozeki-corporate).
This repository is test infrastructure, not the installable WordPress theme.
Do not upload this repository as a theme ZIP.

## Environment map

| Site | Purpose | Theme source |
| --- | --- | --- |
| 8089 | Bilingual company showcase and development | Sibling `../ozeki-corporate`, read-only bind mount |
| 8090 | Distribution ZIP and Theme Unit Test | Installed ZIP, independent database and files |
| 8091 | First-user onboarding and editing tests | Installed development ZIP, independent database and files |
| AWS demo | Existing Japanese showcase and starter preview | Explicit ZIP deployment to the existing site only |

Each local Compose project has separate named volumes. An English/Japanese
editing test saved on 8091 is not the theme's shipped default content. Similarly,
the Japanese AWS showcase uses stored pages, templates and global styles that
are not automatically installed by the theme. The theme includes English-first
starter patterns and an English/Japanese admin guide; users replace the examples
through the normal editor. A theme update does not reset their saved content.

## Prerequisites and first setup

Use Docker with Compose v2 and, for the supplied shell scripts, Bash/WSL Ubuntu.
Clone the theme and this repository into sibling directories. Run the commands
below inside this repository; in Windows, use the WSL shell for Linux commands.
Keep Docker running and ensure the indicated ports and adequate disk space are
available. The bundled database defaults are disposable local-test credentials,
not suitable for an internet-facing installation.

Starting containers does not install WordPress or create an administrator.
On a new volume, open the site's URL and complete WordPress setup using a unique
test administrator/password, then install/activate the theme as described below.
Existing volumes retain their users and content. Administrator credentials are
not documented here: keep them in a password manager, not Git or test logs.

8089 currently binds its port on all host interfaces. Restrict it using the host
firewall when needed; 8090 and 8091 bind to loopback. Do not expose test sites or
their database defaults directly to the internet.

See [showcase fixtures](docs/showcase.md) and [release audit](docs/release-audit.md)
for safe fixture updates, ZIP testing and recorded verification results.

- Development environment: <http://localhost:8089>, with the theme source mounted read-only.
- Release ZIP environment: <http://localhost:8090>, with no source bind mount.

The read-only development mount prevents WordPress administration operations from deleting or overwriting the local theme source.

Start development WordPress:

```sh
docker compose up -d
```

Create or refresh the bilingual test content:

```sh
docker compose run --rm wp-cli wp eval-file /workspace/scripts/seed-content.php
```

Start the independent ZIP test environment:

```sh
docker compose -f docker-compose.ziptest.yml up -d
```

## Build and install the actual ZIP

From this repository:

```sh
bash ../ozeki-corporate/build-release.sh
docker compose -f docker-compose.ziptest.yml run --rm wp-cli wp theme install /release/ozeki-corporate-0.2.0.zip --activate
```

Use the actual versioned filename emitted by the build. The example is the
current 0.2.0 candidate, not the published GitHub v0.1.0.
Record SHA-256 for every tested artifact; filenames/versions alone are insufficient.
For an intentional update of an installed test theme, add `--force` only after
preserving any theme-file modifications. Theme replacement is not a content reset.

For the separate first-user site:

```sh
mkdir -p .audit-tools/starter-release
bash ../ozeki-corporate/build-release.sh "$PWD/.audit-tools/starter-release"
docker compose -f docker-compose.starter.yml up -d
# Complete setup at http://localhost:8091/ if this volume is new, then:
docker compose -f docker-compose.starter.yml run --rm cli wp theme install /release/ozeki-corporate-0.2.0.zip --activate
```

On 8091 open Appearance > Ozeki Corporate Guide, then add a page and select a
starter pattern. Save a draft, reopen it and check image/text replacement.
Connect navigation through Site Editor; the contact example is not a working
form and requires real contact details or a separately configured form.

## Tests and evidence

- [Current guide-update candidate](docs/candidate-2026-09-09.md): September 9
  ZIP hash, eight-case matrix, Theme Check and installed-ZIP guide UI checks.
- [Mac Safari user checks](docs/safari-user-check-2026-09-09.md): verified scope
  and untested accessibility boundaries.

- [AWS 0.2.0 deployment](docs/aws-release-0.2.0-2026-09-07.md): current installed
  AWS version, rollback archive and unchanged-content checks.
- [September 7 candidate validation](docs/release-0.2.0-validation.md): the older
  ZIP deployed to AWS, its eight-case matrix, Theme Check and 8091 checks.
  It does not contain the September 9 guide changes.
- [Showcase](docs/showcase.md): fixture ownership and sample content. Seeding is
  optional and can replace fixture-owned text. Do not rerun it on edited samples
  unless that replacement is intended; never run it on a production site.
- [Release audit](docs/release-audit.md): PHP/WordPress matrix, Theme Check,
  browser and accessibility commands and their historical results.
- [First-user ZIP review](docs/starter-review-2026-09-07.md) and
  [follow-up](docs/starter-followup-2026-09-07.md): photo/text and small-screen tests.
- [Onboarding](docs/onboarding-2026-09-07.md): guide, native page chooser and draft
  round-trip. It is not a full release or accessibility certification.
- [AWS starter preview](docs/aws-starter-preview-2026-09-07.md): existing-site
  boundaries and historical deployment evidence.
- [AWS onboarding update](docs/aws-onboarding-update-2026-09-07.md): latest theme
  deployment, English screenshot and unchanged-content comparison.

Browser scripts require Node and Playwright; accessibility scripts also need
axe-core. `PLAYWRIGHT_MODULE`, `BROWSER_EXE` and, where used, `AXE_SOURCE` can point
to installed tooling. `check-onboarding-ui.cjs` targets only localhost:8091 and
requires `OC_STARTER_PASSWORD` for the disposable `starter_review` account and
`OC_AUDIT_OUTPUT` for artifacts. It creates a local draft, never publishes it.
Other scripts may use different fixture accounts: inspect each script first.
Do not run automated editing tests with production credentials.

Screenshots/logs/ZIPs in `.audit-tools/` are ignored. Commit curated test reports,
not passwords, database dumps, uploads or machine-specific runtime paths.
There are currently no GitHub Actions workflows; a successful push is not a CI
pass. Re-run the release matrix against the final ZIP before release.

## AWS updates

Use the existing authorized WordPress site; no new container or subdomain is
needed. The Japanese homepage and `/theme-starter-preview/` share one site's
theme/settings. Updating the theme must not rerun demo seeding, switch themes,
delete pages, reset global styles or change plugins. Preserve the old theme,
verify the uploaded ZIP hash and compare content/settings before and after.
Keep backups private on the server; a theme archive is not a database backup.
The user's SSH host/key configuration is external to this repository.

The temporary screenshot fixture is only for localhost:8091, does not write to
the database, and must be removed from the container after capture. Never deploy
it to AWS. The English theme screenshot uses real bundled patterns and a sample
site title; it does not promise an automatically imported demo site.

## Stop and resume safely

```sh
docker compose stop
docker compose -f docker-compose.ziptest.yml stop
docker compose -f docker-compose.starter.yml stop
```

Resume the needed project with its corresponding `up -d`. Do not use `down -v`
or volume-pruning commands unless intentionally deleting that test site's data
after backup. Source files, stored WordPress templates, uploaded media and ZIP
artifacts are distinct: back up the appropriate data before destructive tests.

## 日本語での確認ポイント

8089は完成例、8090はZIP検査、8091は初めて使う人の操作確認用です。
AWSの日本語デモと配布テーマの初期表示は同一ではありません。管理画面の
「外観 → Ozeki Corporate Guide」から、ページ作成、写真・文章の差し替え、
メニュー設定、公開前チェックへ進みます。保存したページやテンプレートは
テーマ更新では初期化されません。サンプル投入スクリプトの再実行や
ボリューム削除は、編集内容を失う可能性があるため通常の更新には使いません。
