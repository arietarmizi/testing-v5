<?php

use console\base\Migration;
use common\models\OrderDetail;

/**
 * Handles the creation of table `{{%order_detail}}`.
 */
class m211125_030654_create_order_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(OrderDetail::tableName(), [
            'id'               => $this->string(36)->notNull(),
            'orderId'          => $this->string(36),
            'productVariantId' => $this->string(50),
            'quantity'         => $this->double(53),
            'weight'           => $this->double(53),
            'height'           => $this->double(53),
            'totalWeight'      => $this->double(53),
            'isFreeReturn'     => $this->boolean()->defaultValue(0),
            'productPrice'     => $this->double(53),
            'insurancePrice'   => $this->double(53),
            'subTotalPrice'    => $this->double(53),
            'notes'            => $this->double(53),
            'createdAt'        => $this->dateTime(),
            'updatedAt'        => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('orderDetailId', OrderDetail::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(OrderDetail::tableName());
    }
}
