CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%equipment` (
  `equipment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ispublished` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `asset_id` varchar(255) NOT NULL,
  `created` date NOT NULL,
  `updated` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `staff_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`equipment_id`),
  UNIQUE KEY `asset_id_UNIQUE` (`asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8$

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%equipment_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ispublic` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(125) DEFAULT NULL,
  `description` text NOT NULL,
  `notes` tinytext NOT NULL,
  `created` date NOT NULL,
  `updated` date NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  KEY `ispublic` (`ispublic`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8$

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%equipment_status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) DEFAULT NULL,
  `description` text NOT NULL,
  `image` text,
  `color` varchar(45) DEFAULT NULL,
  `baseline` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8$

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%equipment_ticket` (
  `equipment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`equipment_id`,`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$

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

CREATE TABLE `%TABLE_PREFIX%equipment_config` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL DEFAULT 'undefined',
  `value` varchar(255) NOT NULL DEFAULT 'undefined',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_UNIQUE` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$

REPLACE INTO `%TABLE_PREFIX%equipment_config` (`key`, `value`)
VALUES ('recurrance_enabled','false')$ 

SET SQL_SAFE_UPDATES=0$
DELETE FROM `%TABLE_PREFIX%list` WHERE `name`='equipment_status'$ 
INSERT INTO `%TABLE_PREFIX%list` (`name`, `created`,`notes`,`updated`)
VALUES ('equipment_status',NOW(),'internal equipment plugin list, do not modify',NOW())$ 

DELETE FROM `%TABLE_PREFIX%list` WHERE `name`='equipment'$ 
INSERT INTO `%TABLE_PREFIX%list` (`name`, `created`,`notes`,`updated`)
VALUES ('equipment',NOW(),'internal equipment plugin list, do not modify',NOW())$ 

DELETE FROM `%TABLE_PREFIX%form` WHERE `title`='Equipment'$
INSERT INTO `%TABLE_PREFIX%form` (`type`, `deletable`,`title`, `notes`, `created`, `updated`)
VALUES ('G',0,'Equipment','Equipment internal form',NOW(),NOW())$ 
SET SQL_SAFE_UPDATES=1$

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

call `%TABLE_PREFIX%CreateEquipmentFormFields`$


DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_ADEL`$

CREATE TRIGGER `%TABLE_PREFIX%equipment_ADEL` AFTER DELETE ON `%TABLE_PREFIX%equipment` FOR EACH ROW
BEGIN
	SET @pk=OLD.equipment_id;
	SET @list_pk=(SELECT id FROM `%TABLE_PREFIX%list` WHERE name='equipment');
	DELETE FROM `%TABLE_PREFIX%list_items` WHERE list_id = @list_pk AND properties=CONCAT('',@pk); 
END$


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

DROP VIEW IF EXISTS `%TABLE_PREFIX%EquipmentSearchView`$

CREATE VIEW `%TABLE_PREFIX%EquipmentSearchView` AS 
select `eq`.`equipment_id` AS `equipment_id`,`eq`.`asset_id` AS `asset_id`,`fev`.`value` AS `value` 
from ((`%TABLE_PREFIX%equipment` `eq` 
left join `%TABLE_PREFIX%form_entry` `fe` 
on((`eq`.`equipment_id` = `fe`.`object_id`))) 
left join `%TABLE_PREFIX%form_entry_values` `fev` 
on((`fe`.`id` = `fev`.`entry_id`))) 
where (`fe`.`object_type` = 'E')$

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

DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%Equipment_Copy_Form_Entry`$
CREATE PROCEDURE `%TABLE_PREFIX%Equipment_Copy_Form_Entry`(p_ticket_id INT, p_new_ticket_id INT)
BEGIN
	DECLARE n INT DEFAULT 0;
	DECLARE i INT DEFAULT 0;
	DECLARE l_id INT;
	DECLARE l_new_id INT;

	DROP TEMPORARY TABLE IF EXISTS tmp_table2;
	CREATE TEMPORARY TABLE tmp_table2 
        SELECT * 
        FROM %TABLE_PREFIX%_form_entry 
        WHERE object_id = p_ticket_id AND `object_type` = 'T';

	SET SQL_SAFE_UPDATES=0;

	ALTER TABLE tmp_table2 modify column id int;

	SELECT COUNT(*) FROM tmp_table2 INTO n;
	SET i = 0;

	WHILE i<n DO
		SELECT id INTO l_id FROM tmp_table2 LIMIT i,1;	
		UPDATE tmp_table2 set object_id=p_new_ticket_id, created=NOW(), updated=NOW() WHERE id=l_id;

		INSERT INTO %TABLE_PREFIX%_form_entry 
		(SELECT NULL, form_id, object_id, object_type, sort, created, updated 
		 FROM tmp_table2 WHERE id=l_id); 

		SELECT LAST_INSERT_ID() INTO l_new_id;

		DROP TEMPORARY TABLE IF EXISTS tmp_table3;
		CREATE TEMPORARY TABLE tmp_table3 

		SELECT * FROM %TABLE_PREFIX%_form_entry_values 
		WHERE entry_id = l_id;

		ALTER TABLE tmp_table3 modify column entry_id int;
		UPDATE tmp_table3 SET entry_id = l_new_id;
		INSERT INTO %TABLE_PREFIX%_form_entry_values SELECT * FROM tmp_table3;		
		DROP TEMPORARY TABLE IF EXISTS tmp_table3;

		SET i = i + 1;
	END WHILE;
	
	SET SQL_SAFE_UPDATES=1;
	DROP TEMPORARY TABLE IF EXISTS tmp_table2;
END$


DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%Equipment_Reopen_Ticket`$
CREATE PROCEDURE `%TABLE_PREFIX%Equipment_Reopen_Ticket`(p_etr_id INT)
BEGIN
	DECLARE l_ticket_id INT;
	DECLARE l_new_ticket_id INT;
	DECLARE l_equipment_id INT;
	DECLARE l_ticket_number INT;
	DECLARE l_loop_flag boolean;
	DECLARE l_tmp INT;

	DROP TEMPORARY TABLE IF EXISTS tmp_table1;

	SELECT equipment_id, ticket_id 
	INTO l_equipment_id, l_ticket_id 
	FROM %TABLE_PREFIX%equipment_ticket_recurring 
	WHERE id=p_etr_id;
	
	IF l_ticket_id IS NOT NULL THEN	
		SET l_loop_flag = FALSE;
		WHILE l_loop_flag = FALSE DO
			SET l_tmp = NULL;
			SET l_ticket_number = FLOOR(RAND()*900000)+100000;
			SELECT ticket_id INTO l_tmp FROM %TABLE_PREFIX%ticket WHERE `number` = l_ticket_number;
			IF l_tmp IS NULL THEN
				SET l_loop_flag = TRUE;
			END IF;
		END WHILE;		

		CREATE TEMPORARY TABLE tmp_table1 
                SELECT * 
                FROM %TABLE_PREFIX%ticket 
                WHERE ticket_id = l_ticket_id;

		SET SQL_SAFE_UPDATES=0;
		ALTER TABLE tmp_table1 modify column ticket_id int;
		UPDATE tmp_table1 
		SET `number` = l_ticket_number, 
			ticket_id = NULL, 
			`status` = 'open', 
			closed = NULL, 
			created = NOW(), 
			updated = NOW(),
			isanswered = 0,
			lastmessage = NOW(),
			lastresponse = NULL;
		SET SQL_SAFE_UPDATES=1;		

		INSERT INTO %TABLE_PREFIX%ticket SELECT * FROM tmp_table1;
		DROP TEMPORARY TABLE IF EXISTS tmp_table1;

		SELECT ticket_id INTO l_new_ticket_id FROM %TABLE_PREFIX%ticket WHERE `number` = l_ticket_number;
		IF l_new_ticket_id IS NOT NULL THEN	
			CREATE TEMPORARY TABLE tmp_table1 SELECT * FROM %TABLE_PREFIX%ticket__cdata WHERE ticket_id = l_ticket_id;
			SET SQL_SAFE_UPDATES=0;
			ALTER TABLE tmp_table1 modify column ticket_id int;
			UPDATE tmp_table1 SET ticket_id = l_new_ticket_id;
			SET SQL_SAFE_UPDATES=1;	

			INSERT INTO %TABLE_PREFIX%ticket__cdata SELECT * FROM tmp_table1;
			DROP TEMPORARY TABLE IF EXISTS tmp_table1;

			CALL %TABLE_PREFIX%Equipment_Copy_Form_Entry(l_ticket_id, l_new_ticket_id);

			CREATE TEMPORARY TABLE tmp_table1 SELECT * FROM %TABLE_PREFIX%ticket_event 
			WHERE ticket_id = l_ticket_id
			AND `state`='created';
			SET SQL_SAFE_UPDATES=0;
			UPDATE tmp_table1 SET ticket_id=l_new_ticket_id, `timestamp` = NOW();
			INSERT INTO %TABLE_PREFIX%ticket_event SELECT * FROM tmp_table1;
			SET SQL_SAFE_UPDATES=1;	
			DROP TEMPORARY TABLE IF EXISTS tmp_table1;
			
			
		END IF;
	END IF;
END$

DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%EquipmentCronProc`$
CREATE PROCEDURE `%TABLE_PREFIX%EquipmentCronProc`()
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE l_id INT;
	DECLARE l_next_date datetime;
	DECLARE l_interval double;
	DECLARE cur1 CURSOR FOR (SELECT id, `interval`, next_date
	FROM %TABLE_PREFIX%equipment_ticket_recurring
	WHERE active=1);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
		

	OPEN cur1;

	read_loop: LOOP
		FETCH cur1 INTO l_id, l_interval, l_next_date;
		IF done then
			LEAVE read_loop;
		END IF;

		IF l_next_date <= NOW() 	THEN
			SET l_next_date = DATE_ADD(l_next_date, INTERVAL l_interval SECOND);
			CALL %TABLE_PREFIX%Equipment_Reopen_Ticket(l_id);
			UPDATE %TABLE_PREFIX%equipment_ticket_recurring 
                        SET next_date=l_next_date WHERE id=l_id;
		END IF;
	END LOOP;

	CLOSE cur1;
END$


SET SQL_SAFE_UPDATES=0$
UPDATE `%TABLE_PREFIX%plugin` SET version = '0.3' WHERE `name`='Equipment Manager'$
SET SQL_SAFE_UPDATES=1$	
