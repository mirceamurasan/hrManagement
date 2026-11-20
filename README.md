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
php bin/console doctrine:migrations:migrate
php bin/console app:user:create admin@example.com
php -S 127.0.0.1:8000 -t public public/index.php
```

The account command asks for a password without displaying it and creates an administrator account using Symfony's configured password hasher.

To inspect or import a legacy SAP project CSV, use:

```sh
php bin/console app:import:projects /path/to/projects.csv --dry-run
```

Remove `--dry-run` to write the changes to SQLite. Each completed import is recorded in the administrator's Import history page.

Open `http://127.0.0.1:8000/health` to check that the application responds. The local SQLite file is created under `server/var/` when the database is first used. Put any real local secrets in `.env.local` or the process environment; do not commit them.

## Migration approach

The old application is being replaced feature by feature. See [`MIGRATION.md`](MIGRATION.md) for the current target and sequence. Changes are kept in focused commits so each step can be reviewed independently.
