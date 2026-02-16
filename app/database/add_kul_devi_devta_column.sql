-- =============================================
-- Sanskar AI - Add kul_devi_devta column to SAI_users
-- =============================================
-- Run this SQL command on the database to add the Kul Devi/Devta field.
-- The community_name column already exists in the table.
-- =============================================

ALTER TABLE SAI_users ADD COLUMN kul_devi_devta VARCHAR(150) NULL AFTER community_name;
