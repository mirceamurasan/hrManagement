# HR Management

An incremental modernization of the legacy HR management application.

## Current stack

- PHP 8.6+
- Symfony 7.4 LTS
- Composer
- SQLite is configured as the zero-server local default. No database server setup is required.

The new application lives in [`server/`](server/). The original Symfony 2 application is preserved under [`legacy/server/`](legacy/server/) as a migration reference; it is not part of the new runtime.

## Run the new application

```sh
cd server
cp .env.example .env
composer install
php -S 127.0.0.1:8000 -t public public/index.php
```

Open `http://127.0.0.1:8000/health` to check that the application responds. The local SQLite file is created under `server/var/` when the database is first used. Put any real local secrets in `.env.local` or the process environment; do not commit them.

## Migration approach

The old application is being replaced feature by feature. See [`MIGRATION.md`](MIGRATION.md) for the current target and sequence. Changes are kept in focused commits so each step can be reviewed independently.
