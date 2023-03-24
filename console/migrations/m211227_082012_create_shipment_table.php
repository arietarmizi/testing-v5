<?php

use common\models\Shipment;
use common\models\Shop;
use console\base\Migration;

/**
 * Handles the creation of table `{{%shipment}}`.
 */
class m211227_082012_create_shipment_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable(Shipment::tableName(), [
            'id'                    => $this->string(36)->notNull(),
            'shopId'                => $this->string(36)->notNull(),
            'marketplaceShipmentId' => $this->string(36)->notNull(),
            'name'                  => $this->string(36),
            'isAvailable'           => $this->boolean()->defaultValue(0),
            'status'                => $this->string(50)->defaultValue(Shipment::STATUS_ACTIVE),
            'createdAt'             => $this->dateTime(),
            'updatedAt'             => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('shipmentId', Shipment::tableName(), ['id']);
        $this->addForeignKey('fk_shipment_shop',
            Shipment::tableName(), 'shopId',
            Shop::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable(Shipment::tableName());
    }
}
