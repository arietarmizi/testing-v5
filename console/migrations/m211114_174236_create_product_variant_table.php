<?php

use console\base\Migration;
use common\models\ProductVariant;

/**
 * Handles the creation of table `{{%product_variant}}`.
 */
class m211114_174236_create_product_variant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProductVariant::tableName(), [
            'id'                 => $this->string(36)->notNull(),
            'sku'                => $this->string(100),
            'productId'          => $this->string(36)->notNull(),
            'marketplaceProductVariantId' => $this->string(36)->notNull(),
            'name'               => $this->string(255)->notNull(),
            'sellingStatus'      => $this->string(100)->defaultValue(ProductVariant::SELLING_FOR_SALE),
            'isShelfLife'        => $this->boolean()->defaultValue(0),
            'duration'           => $this->double(53),
            'inboundLimit'       => $this->double(53),
            'outboundLimit'      => $this->double(53),
            'minOrder'           => $this->double(53),
            'description'        => $this->text(),
            'productDescription' => $this->text(),
            'defaultPrice'       => $this->double(53),
            'length'             => $this->double(53),
            'width'              => $this->double(53),
            'height'             => $this->double(53),
            'weight'             => $this->double(53),
            'barcode'            => $this->text(),
            'isPreOrder'         => $this->boolean()->defaultValue(0),
            'minPreOrderDay'     => $this->double(53),
            'discount'           => $this->double(53),
            'warehouseId'        => $this->string(53),
            'isWholesale'        => $this->boolean()->defaultValue(0),
            'isFreeReturn'       => $this->boolean()->defaultValue(0),
            'isMustInsurance'    => $this->boolean()->defaultValue(0),
            'status'             => $this->string(50)->defaultValue(ProductVariant::STATUS_ACTIVE),
            'createdAt'          => $this->dateTime(),
            'updatedAt'          => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productVariantId', ProductVariant::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProductVariant::tableName());
    }
}
