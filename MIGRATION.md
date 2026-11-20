# Modernization plan

## Target

The new runtime targets PHP 8.6 and Symfony 7.4 LTS. The legacy application depends on Symfony 2.4 and bundles that predate current Symfony conventions, so the new runtime is being introduced separately instead of attempting an unsafe in-place dependency upgrade.

## Current structure

- `server/` is the new Symfony application. It uses Doctrine ORM for the migrated persistence layer and a local SQLite file by default, so no database server needs to be installed or configured.
- `legacy/server/` preserves the original application for reference while features are migrated.
- Local secrets belong in ignored `.env.local` files or process environment variables. `.env.example` contains placeholders only.

## Migration sequence

1. Establish the modern runtime and safe configuration defaults.
2. Port domain behavior in small groups, keeping each commit focused. Migrated slices include the database-independent weekly availability calculator in `server/src/Availability/` and a streaming parser for the legacy project CSV export in `server/src/Import/`.
3. Replace legacy authentication, controller, template, import, and reporting flows with supported Symfony components. Sign-in, modern password hashing, login throttling, role hierarchy, command-line creation of the first administrator, and initial project/customer screens are now in place; task planning, user management, and the remaining reports are still to be ported.
4. Port persistence and workflows while keeping SQLite as the no-server default. A production database server can be chosen later without changing domain behavior.

The Doctrine-mapped models now include user accounts, worker profiles, teams, work locations, employers, contract types, contracts, contract periods and vacations, project milestones, project statuses, public holidays, customers, projects, task states, jobs, service providers, project tasks, task allocations, and import history. The migrated screens cover account and customer management, worker, team, and location administration, contract setup and periods, vacation recording, project milestones, task planning and status administration, task allocations by job, provider, and worker, weekly availability reporting, a weekly review of task allocations and vacations, and import history. The SAP project CSV parser is now wired to a streaming import command with dry-run support and persistent import reports. Local SQLite migrations seed a default `Planned` state and `General` job so task planning can start without another setup step. Directory-backed user import and remaining administration flows still need migration.

## Weekly availability calculation

`AvailabilityCalculator` accepts `AvailabilityPeriod` and `VacationPeriod` value objects plus public holidays, so it can be used without Doctrine or a configured database. Callers select periods for a job and resource type before passing them in. Employee calculations subtract non-trashed vacation coefficients on working days. Service-provider calculations count active periods without subtracting employee vacation. Both calculations treat the date range as inclusive and exclude weekends and supplied holidays.

`ProjectCsvParser` reads the old semicolon-delimited project export one row at a time, maps the same SAP columns as the old importer, strips a UTF-8 byte-order mark, and reports whether the fields required to identify a project are present. Database lookups and writes remain outside the parser.

The legacy directory is temporary migration material. It should only be removed after its required behavior has been migrated and reviewed.
