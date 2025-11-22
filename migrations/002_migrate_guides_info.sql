-- Migration: Migrate data from guides_info (if exists) into guides table
-- This script will add missing columns to `guides` and copy data from `guides_info` + `users`.

-- 1) Add extra columns if they don't exist
ALTER TABLE `guides`
  ADD COLUMN IF NOT EXISTS `user_id` INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `languages` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `experience_years` INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `specialized_route` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `health_status` VARCHAR(255) DEFAULT NULL;

-- 2) Copy data from guides_info (only when guides table is empty for same user)
INSERT INTO `guides` (
  `full_name`,`phone`,`email`,`identity_no`,`certificate_no`,`guide_type`,`avatar`,`notes`,`user_id`,`languages`,`experience_years`,`specialized_route`,`health_status`,`created_at`,`updated_at`
)
SELECT
  u.full_name, u.phone, u.email,
  gi.identity_no, gi.certificate_no, gi.guide_type,
  COALESCE(u.avatar, NULL) AS avatar,
  gi.notes, gi.user_id, gi.languages, gi.experience_years, gi.specialized_route, gi.health_status,
  NOW(), NOW()
FROM guides_info gi
JOIN users u ON u.id = gi.user_id
WHERE NOT EXISTS (
  SELECT 1 FROM guides g WHERE g.user_id = gi.user_id
);

-- Note: Review results and remove guides_info if desired.
