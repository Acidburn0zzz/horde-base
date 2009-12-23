-- Replace 'UTF8' with the encoding used in your database.
-- This script is tested with Postgres 8.3+.
ALTER TABLE horde_prefs ALTER COLUMN pref_value TYPE BYTEA USING encode(convert_to(pref_value, 'UTF8'), 'escape');
