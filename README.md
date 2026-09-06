# Corporate Theme Rescue

Isolated Docker environments for Ozeki Corporate.

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
