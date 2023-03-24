<?php

use common\models\Order;
use common\models\OrderStatus;
use console\base\Migration;

/**
 * Class m211228_053052_alter_order_table
 */
class m211228_053052_alter_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(Order::tableName(), 'orderStatus');
        $this->addColumn(Order::tableName(), 'orderStatusId', $this->string(36));
        $this->addForeignKey('fk_order_order_status',
            Order::tableName(), 'orderStatusId',
            OrderStatus::tableName(), 'id',
            'NO ACTION', 'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_order_status', Order::tableName());
        $this->dropColumn(Order::tableName(), 'orderStatusId');
    }
}
