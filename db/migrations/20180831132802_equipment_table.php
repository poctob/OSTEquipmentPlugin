<?php

use Phinx\Migration\AbstractMigration;

class EquipmentTable extends AbstractMigration
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
        $table = $this->table('equipment');
        $table
            ->addColumn('category_id', 'integer', ['null' => false, 'signed' => false, 'default' => 0])
            ->addColumn('status_id', 'integer', ['null' => false, 'signed' => false, 'default' => 0])
            ->addColumn('ispublished', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('asset_id', 'string', ['null' => false])
            ->addColumn('is_active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('staff_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addTimestamps()
            ->addIndex(['asset_id'], ['unique' => true, 'name' => 'asset_id_UNIQUE'])
            ->create();
    }
}
