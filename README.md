# Corporate Theme Rescue

Isolated Docker environments for Ozeki Corporate.

- Development environment: <http://localhost:8089>, with the theme source mounted read-only.
- Release ZIP environment: <http://localhost:8090>, with no source bind mount.

The read-only development mount prevents WordPress administration operations from deleting or overwriting the local theme source.

Start development WordPress:

```sh
docker compose up -d
```

Start the independent ZIP test environment:

```sh
docker compose -f docker-compose.ziptest.yml up -d
```
