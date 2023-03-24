<?php

use common\models\Marketplace;
use common\models\OrderStatus;
use console\base\Migration;

/**
 * Handles the creation of table `{{%order_status}}`.
 */
class m211228_050917_create_order_status_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable(OrderStatus::tableName(), [
            'id'                    => $this->string(36)->notNull(),
            'marketplaceId'         => $this->string(36)->notNull(),
            'marketplaceStatusCode' => $this->string(50)->notNull(),
            'description'           => $this->text(),
            'status'                => $this->string(50)->defaultValue(OrderStatus::STATUS_ACTIVE),
            'createdAt'             => $this->dateTime(),
            'updatedAt'             => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('pk_order_status_id', OrderStatus::tableName(), ['id']);
        $this->addForeignKey('fk_order_status_marketplace',
            OrderStatus::tableName(), 'marketplaceId',
            Marketplace::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable(OrderStatus::tableName());
    }
}
