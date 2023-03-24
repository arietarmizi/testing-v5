<?php

use console\base\Migration;
use common\models\MasterStatus;

/**
 * Handles the creation of table `{{%master_status}}`.
 */
class m211126_005824_create_master_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(MasterStatus::tableName(), [
            'id'            => $this->string(36)->notNull(),
            'marketplaceId' => $this->string(36),
            'statusCode'    => $this->string(100),
            'desc'          => $this->text(),
            'status'        => $this->string(50)->defaultValue(MasterStatus::STATUS_ACTIVE),
            'createdAt'     => $this->dateTime(),
            'updatedAt'     => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('masterStatusId', MasterStatus::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(MasterStatus::tableName());
    }
}
