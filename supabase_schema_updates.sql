-- Supabase Schema Updates

-- 1. Add timestamps to students table
-- This corresponds to the migration: 2026_01_26_141750_add_timestamps_to_students_table.php
ALTER TABLE students ADD COLUMN IF NOT EXISTS created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL;
ALTER TABLE students ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL;

-- Note: The following changes were made in the codebase to support Postgres compatibility
-- but do not require schema changes:
-- - Replaced SQLite-specific DATE_FORMAT with Postgres-compatible TO_CHAR in AuthLoginController.php
-- - Added Supabase Key fallback in FeeManagementService.php
