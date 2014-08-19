DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ost_Equipment_Copy_Form_Entry`(p_ticket_id INT, p_new_ticket_id INT)
BEGIN
	DECLARE n INT DEFAULT 0;
	DECLARE i INT DEFAULT 0;
	DECLARE l_id INT;
	DECLARE l_new_id INT;

	DROP TEMPORARY TABLE IF EXISTS tmp_table2;
	CREATE TEMPORARY TABLE tmp_table2 SELECT * FROM ost_form_entry WHERE object_id = p_ticket_id AND `type` = 'T';

	SET SQL_SAFE_UPDATES=0;

	ALTER TABLE tmp_table2 modify column id int;

	SELECT COUNT(*) FROM tmp_table2 INTO n;
	SET i = 0;

	WHILE i<n DO
		SELECT id INTO l_id FROM tmp_table2 LIMIT i,1;	
		UPDATE tmp_table2 set created=NOW(), updated=NOW() WHERE id=l_id;

		INSERT INTO ost_form_entry 
		(SELECT NULL, form_id, object_id, object_type, sort, created, updated 
		 FROM tmp_table2 WHERE id=l_id); 

		SELECT LAST_INSERT_ID() INTO l_new_id;

		DROP TEMPORARY TABLE IF EXISTS tmp_table3;
		CREATE TEMPORARY TABLE tmp_table3 

		SELECT * FROM ost_form_entry_values 
		WHERE entry_id = l_id;

		ALTER TABLE tmp_table3 modify column entry_id int;
		UPDATE tmp_table3 SET entry_id = l_new_id;
		INSERT INTO ost_form_entry_values SELECT * FROM tmp_table3;		
		DROP TEMPORARY TABLE IF EXISTS tmp_table3;

		SET i = i + 1;
	END WHILE;
	
	SET SQL_SAFE_UPDATES=1;
	DROP TEMPORARY TABLE IF EXISTS tmp_table2;


END$$
DELIMITER ;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ost_Equipment_Reopen_Ticket`(p_etr_id INT)
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
	FROM ost_equipment_ticket_recurring 
	WHERE id=p_etr_id;
	
	IF l_ticket_id IS NOT NULL THEN	
		SET l_loop_flag = FALSE;
		WHILE l_loop_flag = FALSE DO
			SET l_tmp = NULL;
			SET l_ticket_number = FLOOR(RAND()*900000)+100000;
			SELECT ticket_id INTO l_tmp FROM ost_ticket WHERE `number` = l_ticket_number;
			IF l_tmp IS NULL THEN
				SET l_loop_flag = TRUE;
			END IF;
		END WHILE;		

		CREATE TEMPORARY TABLE tmp_table1 SELECT * FROM ost_ticket WHERE ticket_id = l_ticket_id;

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

		INSERT INTO ost_ticket SELECT * FROM tmp_table1;
		DROP TEMPORARY TABLE IF EXISTS tmp_table1;

		SELECT ticket_id INTO l_new_ticket_id FROM ost_ticket WHERE `number` = l_ticket_number;
		IF l_new_ticket_id IS NOT NULL THEN	
			CREATE TEMPORARY TABLE tmp_table1 SELECT * FROM ost_ticket__cdata WHERE ticket_id = l_ticket_id;
			SET SQL_SAFE_UPDATES=0;
			ALTER TABLE tmp_table1 modify column ticket_id int;
			UPDATE tmp_table1 SET ticket_id = l_new_ticket_id;
			SET SQL_SAFE_UPDATES=1;	

			INSERT INTO ost_ticket__cdata SELECT * FROM tmp_table1;
			DROP TEMPORARY TABLE IF EXISTS tmp_table1;

			CALL ost_Equipment_Copy_Form_Entry(l_ticket_id, l_new_ticket_id);

			CREATE TEMPORARY TABLE tmp_table1 SELECT * FROM ost_ticket_event 
			WHERE ticket_id = l_ticket_id
			AND `state`='created';
			SET SQL_SAFE_UPDATES=0;
			UPDATE tmp_table1 SET ticket_id=l_new_ticket_id, `timestamp` = NOW();
			INSERT INTO ost_ticket_event SELECT * FROM tmp_table1;
			SET SQL_SAFE_UPDATES=1;	
			DROP TEMPORARY TABLE IF EXISTS tmp_table1;
			
			
		END IF;
	END IF;
END$$
DELIMITER ;

