<?php

use common\models\Order;
use common\models\ShipmentService;
use console\base\Migration;

/**
 * Class m211228_041131_add_column_shipmenServiceId_order_table
 */
class m211228_041131_add_column_shipmentServiceId_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Order::tableName(), 'shipmentServiceId', $this->string(36));
        $this->addForeignKey('fk_order_shipment_service',
            Order::tableName(), 'shipmentServiceId',
            ShipmentService::tableName(), 'id',
            'NO ACTION', 'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_shipment_service', Order::tableName());
        $this->dropColumn(Order::tableName(), 'shipmentServiceId');
    }
}
