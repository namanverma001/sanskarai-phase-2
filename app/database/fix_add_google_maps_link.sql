-- ============================================================================
-- SANSKAR AI - Production Database Fix: Add google_maps_link column
-- ============================================================================
-- Date     : 2026-05-24
-- Issue    : Fatal error on invitation creation - column 'google_maps_link' 
--            not found in SAI_invitations table
-- 
-- INSTRUCTIONS:
--   Run this SQL on the PRODUCTION database to fix the error.
--   This is safe to run - it uses IF NOT EXISTS to avoid errors if
--   the column already exists.
-- ============================================================================

-- Add the missing google_maps_link column to SAI_invitations
ALTER TABLE SAI_invitations
    ADD COLUMN IF NOT EXISTS google_maps_link VARCHAR(500) NULL DEFAULT NULL AFTER venue;

-- ============================================================================
-- VERIFICATION: Run this after the migration to confirm the column exists
-- ============================================================================
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'SAI_invitations' AND COLUMN_NAME = 'google_maps_link';
-- ============================================================================
