<?php

use common\models\Shipment;
use common\models\ShipmentService;
use console\base\Migration;

/**
 * Handles the creation of table `{{%shipment_service}}`.
 */
class m211227_083739_create_shipment_service_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable(ShipmentService::tableName(), [
            'id'                           => $this->string(36)->notNull(),
            'shipmentId'                   => $this->string(36)->notNull(),
            'marketplaceShipmentServiceId' => $this->string(36)->notNull(),
            'name'                         => $this->string(36),
            'isAvailable'                  => $this->boolean()->defaultValue(0),
            'status'                       => $this->string(50)->defaultValue(ShipmentService::STATUS_ACTIVE),
            'createdAt'                    => $this->dateTime(),
            'updatedAt'                    => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('shipmentServiceId', ShipmentService::tableName(), ['id']);
        $this->addForeignKey('fk_shipment_service_shipment',
            ShipmentService::tableName(), 'shipmentId',
            Shipment::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable(ShipmentService::tableName());
    }
}
