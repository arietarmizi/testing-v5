<?php

use console\base\Migration;
use common\models\ProductBundle;

/**
 * Handles the creation of table `{{%product_bundle}}`.
 */
class m211114_190241_create_product_bundle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProductBundle::tableName(), [
            'id'          => $this->string(36)->notNull(),
            'name'        => $this->string(255)->notNull(),
            'price'       => $this->double(53)->notNull(),
            'description' => $this->text(),
            'status'      => $this->string(50)->defaultValue(ProductBundle::STATUS_ACTIVE),
            'createdAt'   => $this->dateTime(),
            'updatedAt'   => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productBundleId', ProductBundle::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProductBundle::tableName());
    }
}
