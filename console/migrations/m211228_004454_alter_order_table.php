<?php

use common\models\Order;
use common\models\Shipment;
use console\base\Migration;

/**
 * Class m211228_004454_alter_order_table
 */
class m211228_004454_alter_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(Order::tableName(), 'courierId', 'shipmentId');
        $this->addForeignKey('fk_order_shipment',
            Order::tableName(), 'shipmentId',
            Shipment::tableName(), 'id',
            'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_shipment', Order::tableName());
        $this->dropColumn(Order::tableName(), 'shipmentId');
    }
}
