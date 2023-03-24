<?php

use console\base\Migration;
use common\models\ProductBundleDetail;

/**
 * Handles the creation of table `{{%product_bundle_detail}}`.
 */
class m211114_193454_create_product_bundle_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProductBundleDetail::tableName(), [
            'id'               => $this->string(36)->notNull(),
            'productBundleId'  => $this->string(36),
            'productVariantId' => $this->string(36),
            'quantity'         => $this->double(53),
            'createdAt'        => $this->dateTime(),
            'updatedAt'        => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productBundleDetailId', ProductBundleDetail::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProductBundleDetail::tableName());
    }
}
