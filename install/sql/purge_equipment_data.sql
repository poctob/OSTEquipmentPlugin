SET SQL_SAFE_UPDATES=0$
DELETE FROM `%TABLE_PREFIX%equipment`$ 
DELETE FROM `%TABLE_PREFIX%equipment_category`$
DELETE FROM `%TABLE_PREFIX%equipment_status`$
DELETE FROM `%TABLE_PREFIX%equipment_ticket`$
DELETE FROM `%TABLE_PREFIX%equipment_ticket_recurring`$
DELETE FROM `%TABLE_PREFIX%equipment_config`$
DELETE FROM `%TABLE_PREFIX%list` WHERE `name`='equipment_status'$ 
DELETE FROM `%TABLE_PREFIX%list` WHERE `name`='equipment'$ 
DELETE FROM `%TABLE_PREFIX%form` WHERE `title`='Equipment'$
SET SQL_SAFE_UPDATES=1$
