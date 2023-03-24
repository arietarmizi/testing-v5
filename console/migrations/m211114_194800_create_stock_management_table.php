<?php

use console\base\Migration;
use common\models\StockManagement;

/**
 * Handles the creation of table `{{%product_stock}}`.
 */
class m211114_194800_create_stock_management_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(StockManagement::tableName(), [
            'id'               => $this->string(36)->notNull(),
            'warehouseId'      => $this->string(36),
            'productVariantId' => $this->string(36),
            'promotionStock'   => $this->double(53),
            'orderedStock'     => $this->double(50),
            'availableStock'   => $this->double(53),
            'onHandStock'      => $this->double(53),
            'stockAlert'       => $this->double(53),
            'stockType'        => $this->string(36),
            'status'           => $this->string(50)->defaultValue(StockManagement::STATUS_ACTIVE),
            'createdAt'        => $this->dateTime(),
            'updatedAt'        => $this->dateTime(),
        ], $this->tableOptions);


        $this->addPrimaryKey('productStockId', StockManagement::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(StockManagement::tableName());
    }
}
