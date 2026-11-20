# Modernization plan

## Target

The new runtime targets PHP 8.5 and Symfony 7.4 LTS. The legacy application depends on Symfony 2.4 and bundles that predate current Symfony conventions, so the new runtime is being introduced separately instead of attempting an unsafe in-place dependency upgrade.

## Current structure

- `server/` is the new Symfony application. It has no ORM, database URL, or database server requirement yet.
- `legacy/server/` preserves the original application for reference while features are migrated.
- Local secrets belong in ignored `.env.local` files or process environment variables. `.env.example` contains placeholders only.

## Migration sequence

1. Establish the modern runtime and safe configuration defaults.
2. Port domain behavior in small groups, keeping each commit focused. The first migrated slice is the database-independent weekly availability calculator in `server/src/Availability/`.
3. Replace legacy authentication, controller, template, import, and reporting flows with supported Symfony components.
4. Add persistence only when the user chooses how the application should store data; this modernization does not require setting up a database server.

## Weekly availability calculation

`AvailabilityCalculator` accepts contract periods and public holidays as data, so it can be used without Doctrine or a configured database. Callers select periods for a job and resource type before passing them in. Employee calculations subtract non-trashed vacation coefficients on working days. Service-provider calculations count active periods without subtracting employee vacation. Both calculations treat the date range as inclusive and exclude weekends and supplied holidays.

The legacy directory is temporary migration material. It should only be removed after its required behavior has been migrated and reviewed.
