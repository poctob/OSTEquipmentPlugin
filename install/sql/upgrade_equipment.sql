ALTER TABLE `%TABLE_PREFIX%equipment_category` 
ADD COLUMN `parent_id` INT NOT NULL DEFAULT 0 AFTER `updated`$


CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%equipment_ticket_recurring` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipment_id` int(11) NOT NULL,
  `last_opened` datetime DEFAULT NULL,
  `next_date` datetime DEFAULT NULL,
  `interval` double NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$

DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%CreateEquipmentFormFields`$

CREATE PROCEDURE `%TABLE_PREFIX%CreateEquipmentFormFields`()
BEGIN
	SET @form_id = (SELECT id FROM `%TABLE_PREFIX%form` WHERE title='Equipment');
	SET @status_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment_status');
	SET @equipment_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment');

	IF (@form_id IS NOT NULL) AND (@status_list_id IS NOT NULL) AND (@equipment_list_id IS NOT NULL) then
		INSERT INTO `%TABLE_PREFIX%form_field`
			(`form_id`,
			`type`,
			`label`,
			`required`,
			`private`,
			`edit_mask`,
			`name`,
			`sort`,
			`created`,
			`updated`)
			VALUES
			(@form_id,
			CONCAT('list-',@equipment_list_id),
			'Equipment',
			0,0,0,
			'equipment',			
			3,			
			NOW(),
			NOW());	

		INSERT INTO `%TABLE_PREFIX%form_field`
			(`form_id`,
			`type`,
			`label`,
			`required`,
			`private`,
			`edit_mask`,
			`name`,
			`sort`,
			`created`,
			`updated`)
			VALUES
			(@form_id,
			CONCAT('list-',@status_list_id),
			'Status',
			0,0,0,
			'status',			
			2,			
			NOW(),
			NOW());	

                INSERT INTO `%TABLE_PREFIX%form_field`
			(`form_id`,
			`type`,
			`label`,
			`required`,
			`private`,
			`edit_mask`,
			`name`,
			`sort`,
			`created`,
			`updated`)
			VALUES
			(@form_id,
			('text'),
			'Asset ID',
			0,0,0,
			'asset_id',			
			1,			
			NOW(),
			NOW());							
	END IF;
END$

DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%UpgradeEquipmentFormFields`$

CREATE PROCEDURE `%TABLE_PREFIX%UpgradeEquipmentFormFields`()
BEGIN
	SET @form_id = (SELECT id FROM `%TABLE_PREFIX%form` WHERE title='Equipment');
	SET @status_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment_status');
	SET @equipment_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment');

	IF (@form_id IS NOT NULL) AND (@status_list_id IS NOT NULL) AND (@equipment_list_id IS NOT NULL) then			

                INSERT INTO `%TABLE_PREFIX%form_field`
			(`form_id`,
			`type`,
			`label`,
			`required`,
			`private`,
			`edit_mask`,
			`name`,
			`sort`,
			`created`,
			`updated`)
			VALUES
			(@form_id,
			('text'),
			'Asset ID',
			0,0,0,
			'asset_id',			
			1,			
			NOW(),
			NOW());							
	END IF;
END$

call `%TABLE_PREFIX%UpgradeEquipmentFormFields`$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_AINS`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_AINS` AFTER INSERT ON `%TABLE_PREFIX%equipment` FOR EACH ROW
BEGIN
	SET @pk=NEW.equipment_id;
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment');
	INSERT INTO `%TABLE_PREFIX%list_items` (list_id, `value`, `properties`)
	VALUES (@list_pk, CONCAT(' Asset_ID:', NEW.asset_id), CONCAT('',@pk));  
END$


DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_AUPD`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_AUPD` AFTER UPDATE ON `%TABLE_PREFIX%equipment` FOR EACH ROW
BEGIN
	SET @pk=NEW.equipment_id;
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment');

	IF NEW.is_active = 0 THEN
		DELETE FROM `%TABLE_PREFIX%list_items` WHERE list_id = @list_pk AND properties=CONCAT('',@pk);
	ELSE
		SET @list_item_pkid = (SELECT id 
							   FROM `%TABLE_PREFIX%list_items`
							   WHERE list_id = @list_pk AND properties=CONCAT('',@pk));

		IF (@list_item_pkid IS NOT NULL) AND (@list_item_pkid>0) THEN
			UPDATE `%TABLE_PREFIX%list_items` SET `value`= CONCAT(' Asset_ID:', NEW.asset_id) 
			WHERE `properties`= CONCAT('',@pk) AND list_id=@list_pk;
		ELSE
			INSERT INTO `%TABLE_PREFIX%list_items` (list_id, `value`, `properties`)
			VALUES (@list_pk, CONCAT(' Asset_ID:', NEW.asset_id), CONCAT('',@pk));			
		END IF;
	END IF; 
END$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_AINS`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_status_AINS` AFTER INSERT ON `%TABLE_PREFIX%equipment_status` FOR EACH ROW
BEGIN
	SET @pk=NEW.status_id;
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment_status');
	INSERT INTO `%TABLE_PREFIX%list_items` (list_id, `value`, `properties`) 
	VALUES (@list_pk, NEW.name, CONCAT('',@pk));
END$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_AUPD`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_status_AUPD` AFTER UPDATE ON `%TABLE_PREFIX%equipment_status` FOR EACH ROW
BEGIN
	SET @pk=NEW.status_id;
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment_status'); 
	UPDATE `%TABLE_PREFIX%list_items` SET `value`= NEW.name WHERE `properties`= CONCAT('',@pk) AND list_id=@list_pk;
END$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_ADEL`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_status_ADEL` AFTER DELETE ON `%TABLE_PREFIX%equipment_status` FOR EACH ROW
BEGIN
	SET @pk=OLD.status_id; 
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment_status');
	DELETE FROM `%TABLE_PREFIX%list_items` WHERE list_id = @list_pk AND properties=CONCAT('',@pk);
END$

DROP VIEW IF EXISTS `%TABLE_PREFIX%EquipmentFormView`$

CREATE VIEW `%TABLE_PREFIX%EquipmentFormView` AS 
select `%TABLE_PREFIX%form`.`title` AS `title`,
`%TABLE_PREFIX%form_entry`.`id` AS `entry_id`,
`%TABLE_PREFIX%form_entry`.`object_id` AS `ticket_id`,
`%TABLE_PREFIX%form_field`.`id` AS `field_id`,
`%TABLE_PREFIX%form_field`.`label` AS `field_label`,
`%TABLE_PREFIX%form_entry_values`.`value` AS `value`,
`%TABLE_PREFIX%equipment_status`.`status_id` AS `status_id` 
from ((((`%TABLE_PREFIX%form_field` 
left join 
(`%TABLE_PREFIX%form_entry_values` join `%TABLE_PREFIX%form_entry`) 
on(((`%TABLE_PREFIX%form_field`.`id` = `%TABLE_PREFIX%form_entry_values`.`field_id`) and 
(`%TABLE_PREFIX%form_entry`.`id` = `%TABLE_PREFIX%form_entry_values`.`entry_id`)))) 
left join `%TABLE_PREFIX%form` 
on((`%TABLE_PREFIX%form`.`id` = `%TABLE_PREFIX%form_field`.`form_id`))) 
left join `%TABLE_PREFIX%equipment_status` 
on((`%TABLE_PREFIX%form_entry_values`.`value` = `%TABLE_PREFIX%equipment_status`.`name`)))) 
where ((`%TABLE_PREFIX%form`.`title` = 'Equipment') and 
(`%TABLE_PREFIX%form`.`id` = `%TABLE_PREFIX%form_entry`.`form_id`) and 
(`%TABLE_PREFIX%form_entry`.`object_type` = 'T'))$

DROP VIEW IF EXISTS `%TABLE_PREFIX%EquipmentTicketView`$

CREATE VIEW `%TABLE_PREFIX%EquipmentTicketView` AS 
select `%TABLE_PREFIX%equipment_ticket`.`equipment_id` AS `equipment_id`,
`%TABLE_PREFIX%equipment_ticket`.`ticket_id` AS `ticket_id`,
`%TABLE_PREFIX%equipment_ticket`.`created` AS `created`,
`%TABLE_PREFIX%equipment`.`category_id` AS `category_id`,
`%TABLE_PREFIX%equipment`.`is_active` AS `is_active`,
`%TABLE_PREFIX%ticket`.`status` AS `status` 
from ((`%TABLE_PREFIX%equipment_ticket` 
left join `%TABLE_PREFIX%equipment` 
on((`%TABLE_PREFIX%equipment_ticket`.`equipment_id` = `%TABLE_PREFIX%equipment`.`equipment_id`))) 
left join `%TABLE_PREFIX%ticket` 
on((`%TABLE_PREFIX%equipment_ticket`.`ticket_id` = `%TABLE_PREFIX%ticket`.`ticket_id`)))$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%ticket_event_AINS`$

CREATE TRIGGER `%TABLE_PREFIX%ticket_event_AINS` AFTER INSERT ON `%TABLE_PREFIX%ticket_event` FOR EACH ROW
BEGIN
	IF NEW.state='closed' THEN
		SET @equipment_id = (SELECT equipment_id FROM `%TABLE_PREFIX%equipment_ticket`
							WHERE ticket_id=NEW.ticket_id LIMIT 1);
		IF ((@equipment_id IS NOT NULL) AND (@equipment_id>0)) THEN
			SET @status_id = (SELECT status_id FROM `%TABLE_PREFIX%equipment_status`
							WHERE baseline=1 LIMIT 1);

			IF ((@status_id IS NOT NULL) AND (@status_id>0)) THEN
				UPDATE `%TABLE_PREFIX%equipment` SET status_id = @status_id
				WHERE equipment_id = @equipment_id; 
			END IF;
		END IF;

	ELSEIF NEW.state='created' THEN
		
		SET @status_id = (SELECT status_id FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
						ticket_id= NEW.ticket_id AND field_label='Status' LIMIT 1);
                
                SET @asset_id = (SELECT value FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
							ticket_id= NEW.ticket_id AND field_label='Asset ID' LIMIT 1);
		IF( @asset_id IS NULL) THEN
			SET @asset_id_str = (SELECT value FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
							ticket_id= NEW.ticket_id AND field_label='Equipment' LIMIT 1);
			SET @asset_id = (SELECT SUBSTRING_INDEX(@asset_id_str, 'Asset_ID:', -1));
		END IF;

		
		SET @equipment_id = (SELECT equipment_id FROM `%TABLE_PREFIX%equipment` WHERE 
							asset_id= @asset_id);	

		IF ((@status_id IS NOT NULL) AND 
			(@status_id >0)) AND 
			((@equipment_id IS NOT NULL) AND 
			(@equipment_id >0)) THEN						
				
				UPDATE `%TABLE_PREFIX%equipment` SET status_id = @status_id WHERE equipment_id=@equipment_id;
				INSERT INTO `%TABLE_PREFIX%equipment_ticket` (equipment_id, ticket_id, created) 
				VALUES (@equipment_id, NEW.ticket_id, NOW());
		END IF;
	
	END IF;
END$	

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%ticket_event_AUPD`$
CREATE TRIGGER `%TABLE_PREFIX%ticket_event_AUPD` AFTER UPDATE ON `%TABLE_PREFIX%ticket_event` FOR EACH ROW
BEGIN
		SET @status_id = (SELECT status_id FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
						ticket_id= NEW.ticket_id AND field_label='Status' LIMIT 1);
                
                SET @asset_id = (SELECT value FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
							ticket_id= NEW.ticket_id AND field_label='Asset ID' LIMIT 1);
		IF( @asset_id IS NULL) THEN
			SET @asset_id_str = (SELECT value FROM `%TABLE_PREFIX%EquipmentFormView` WHERE 
							ticket_id= NEW.ticket_id AND field_label='Equipment' LIMIT 1);
			SET @asset_id = (SELECT SUBSTRING_INDEX(@asset_id_str, 'Asset_ID:', -1));
		END IF;

		
		SET @equipment_id = (SELECT equipment_id FROM `%TABLE_PREFIX%equipment` WHERE 
							asset_id= @asset_id);	

		IF ((@status_id IS NOT NULL) AND 
			(@status_id >0)) AND 
			((@equipment_id IS NOT NULL) AND 
			(@equipment_id >0)) THEN						
				
				UPDATE `%TABLE_PREFIX%equipment` SET status_id = @status_id WHERE equipment_id=@equipment_id;
		END IF;
	
	
END$

DROP EVENT IF EXISTS `%TABLE_PREFIX%EquipmentCron`$
CREATE EVENT `%TABLE_PREFIX%EquipmentCron`
    ON SCHEDULE EVERY 1 MINUTE
    DO
      CALL `%TABLE_PREFIX%EquipmentCron`()$

DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%update_version`$
CREATE PROCEDURE `%TABLE_PREFIX%update_version`(plugin_name VARCHAR(250), plugin_version VARCHAR(100))
begin
	SET @id = (SELECT id FROM `%TABLE_PREFIX%plugin` WHERE `name`= plugin_name);
	UPDATE `%TABLE_PREFIX%plugin` SET version = plugin_version WHERE id=@id;
END$

call `%TABLE_PREFIX%update_version`('Equipment Manager', '0.3')$