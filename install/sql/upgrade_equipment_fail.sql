ALTER TABLE `%TABLE_PREFIX%equipment` 
CHANGE COLUMN `name` `asset_id` VARCHAR(255) NOT NULL ,
ADD COLUMN `staff_id` INT NULL AFTER `is_active`,
ADD COLUMN `user_id` INT NULL AFTER `staff_id`
DROP INDEX `name`$

ALTER TABLE `%TABLE_PREFIX%equipment_category` 
ADD COLUMN `parent_id` INT NOT NULL DEFAULT 0 AFTER `updated`$