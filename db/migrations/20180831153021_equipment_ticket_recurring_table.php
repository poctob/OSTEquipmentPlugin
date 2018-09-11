<?php

use Phinx\Migration\AbstractMigration;

class EquipmentTicketRecurringTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('equipment_ticket_recurring');
        $table
            ->addColumn('ticket_id', 'integer', ['null' => false])
            ->addColumn('equipment_id', 'integer', ['null' => false])
            ->addColumn('last_opened', 'datetime', ['default' => null])
            ->addColumn('next_date', 'datetime', ['default' => null])
            ->addColumn('interval', 'biginteger', ['null' => false])
            ->addColumn('active', 'boolean', ['null' => false, 'default' => false])
            ->create();

    }
}
