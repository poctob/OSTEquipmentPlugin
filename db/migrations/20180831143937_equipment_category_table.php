<?php

use Phinx\Migration\AbstractMigration;

class EquipmentCategoryTable extends AbstractMigration
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
        $table = $this->table('equipment_category');
        $table
            ->addColumn('ispublic', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('name', 'string', ['default' => null])
            ->addColumn('description', 'text')
            ->addColumn('notes', 'text')
            ->addColumn('parent_id', 'integer', ['null' => false, 'default' => 0])
            ->addTimestamps()
            ->addIndex(['ispublic'])
            ->create();
    }
}
