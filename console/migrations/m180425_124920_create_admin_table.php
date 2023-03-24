<?php

use common\models\Admin;
use console\base\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m180425_124920_create_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Admin::tableName(), [
            'id'                 => $this->string(36)->notNull()->unsigned(),
            'name'               => $this->string(100)->notNull(),
            'email'              => $this->string(255)->notNull()->unique(),
            'phoneNumber'        => $this->string(20)->notNull()->unique(),
            'passwordHash'       => $this->string(255),
            'passwordResetToken' => $this->string(255),
            'authKey'            => $this->string(255),
            'status'             => $this->string(50)->defaultValue(Admin::STATUS_ACTIVE),
            'createdAt'          => $this->dateTime(),
            'updatedAt'          => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('adminId', Admin::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Admin::tableName());
    }
}
