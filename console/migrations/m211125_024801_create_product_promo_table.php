<?php

use console\base\Migration;
use common\models\ProductPromo;

/**
 * Handles the creation of table `{{%product_promo}}`.
 */
class m211125_024801_create_product_promo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProductPromo::tableName(), [
            'id'               => $this->string(36)->notNull(),
            'productVariantId' => $this->string(36)->notNull(),
            'minQuantity'      => $this->double(53),
            'maxQuantity'      => $this->double(53),
            'defaultPrice'     => $this->double(53),
            'status'           => $this->string(50)->defaultValue(ProductPromo::STATUS_ACTIVE),
            'createdAt'        => $this->dateTime(),
            'updatedAt'        => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productPromoId', ProductPromo::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProductPromo::tableName());
    }
}
