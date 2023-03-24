<?php

use console\base\Migration;
use common\models\ProductDiscount;

/**
 * Handles the creation of table `{{%product_discount}}`.
 */
class m211125_025115_create_product_discount_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProductDiscount::tableName(), [
            'id'                 => $this->string(36)->notNull(),
            'productVariantId'   => $this->string(36),
            'discountPrice'      => $this->double(53),
            'discountPercentage' => $this->double(53),
            'startTime'          => $this->dateTime(),
            'endTime'            => $this->dateTime(),
            'initialQuota'       => $this->double(53),
            'remainingQuota'     => $this->double(53),
            'maxOrder'           => $this->double(53),
            'slashPriceStatusId' => $this->string(36),
            'useWarehouse'       => $this->boolean()->defaultValue(0),
            'status'             => $this->string(50)->defaultValue(ProductDiscount::STATUS_ACTIVE),
            'createdAt'          => $this->dateTime(),
            'updatedAt'          => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productDiscountId', ProductDiscount::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProductDiscount::tableName());
    }
}
