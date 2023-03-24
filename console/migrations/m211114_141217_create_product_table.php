<?php

use common\models\ProductCategory;
use console\base\Migration;
use common\models\Product;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m211114_141217_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Product::tableName(), [
            'id'                   => $this->string(36)->notNull(),
            'marketplaceProductId' => $this->string(36),
            'shopId'               => $this->string(36),
            'productCategoryId' => $this->string(36),
            'code'                 => $this->string(50),
            'name'                 => $this->string(255)->notNull(),
            'condition'            => $this->string(50)->defaultValue(Product::CONDITION_NEW),
            'minOrder'             => $this->double(53),
            'productDescription'   => $this->text(),
            'description'          => $this->text(),
            'isMaster'             => $this->boolean()->defaultValue(0),
            'status'               => $this->string(50)->defaultValue(Product::STATUS_ACTIVE),
            'createdAt'            => $this->dateTime(),
            'updatedAt'            => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('productId', Product::tableName(), ['id']);
				$this->addForeignKey('fk_product_product_category',
						Product::tableName(), 'productCategoryId',
						ProductCategory::tableName(), 'id',
						'CASCADE', 'CASCADE'
			);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Product::tableName());
    }
}
