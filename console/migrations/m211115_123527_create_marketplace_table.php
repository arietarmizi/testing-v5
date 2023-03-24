<?php

use console\base\Migration;
use common\models\Marketplace;

/**
 * Handles the creation of table `{{%marketplace}}`.
 */
class m211115_123527_create_marketplace_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Marketplace::tableName(), [
            'id'              => $this->string(36)->notNull(),
            'code'            => $this->string(50),
            'merchantId'      => $this->string(36),
            'marketplaceName' => $this->string(255)->notNull(),
            'description'     => $this->text(),
            'status'          => $this->string(50)->defaultValue(Marketplace::STATUS_ACTIVE),
            'createdAt'       => $this->dateTime(),
            'updatedAt'       => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('marketplaceId', Marketplace::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Marketplace::tableName());
    }
}
