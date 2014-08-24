DROP EVENT IF EXISTS `%TABLE_PREFIX%EquipmentCron`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%EquipmentCronProc`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%Equipment_Reopen_Ticket`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%Equipment_Copy_Form_Entry`$

DROP TRIGGER IF EXISTS `%TABLE_PREFIX%ticket_event_AINS`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%ticket_event_AUPD`$
DROP VIEW IF EXISTS `%TABLE_PREFIX%EquipmentFormView`$
DROP VIEW IF EXISTS `%TABLE_PREFIX%EquipmentTicketView`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_ADEL`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_AUPD`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_status_AINS`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_AUPD`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_AINS`$
DROP TRIGGER IF EXISTS `%TABLE_PREFIX%equipment_ADEL`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%CreateEquipmentFormFields`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%UpgradeEquipmentFormFields`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment_ticket`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment_status`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment_category`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment_ticket_recurring`$
DROP TABLE IF EXISTS `%TABLE_PREFIX%equipment_config`$


CREATE PROCEDURE `%TABLE_PREFIX%RemoveEquipmentFormFields`()
BEGIN
    SET @form_id = (SELECT id FROM `%TABLE_PREFIX%form` WHERE title='Equipment');
    SET @status_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment_status');
    SET @equipment_list_id = (SELECT id FROM `%TABLE_PREFIX%list` WHERE `name`='equipment');

    DELETE FROM `%TABLE_PREFIX%list_items` WHERE list_id = @status_list_id;
    DELETE FROM `%TABLE_PREFIX%list_items` WHERE list_id = @equipment_list_id;

    DELETE FROM `%TABLE_PREFIX%list` WHERE id = @status_list_id;
    DELETE FROM `%TABLE_PREFIX%list` WHERE id = @equipment_list_id;

    DELETE FROM `%TABLE_PREFIX%form_entry` WHERE form_id = @form_id;
    DELETE FROM `%TABLE_PREFIX%form_field` WHERE form_id = @form_id;

    DELETE FROM `%TABLE_PREFIX%form` WHERE id = @form_id;
END$

CALL `%TABLE_PREFIX%RemoveEquipmentFormFields`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%RemoveEquipmentFormFields`$
DROP PROCEDURE IF EXISTS `%TABLE_PREFIX%update_version`$




