<?php

use common\models\Product;
use common\models\ProductImages;
use common\models\ProductVariant;
use common\models\ProductVariantImages;
use common\models\Shipment;
use common\models\Shop;
use console\base\Migration;

/**
 * Handles the creation of table `{{%shipment}}`.
 */
class m211228_075225_create_product_images_table extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(ProductImages::tableName(), [
			'id'                    => $this->string(36)->notNull(),
			'fileId'                => $this->string(36),
			'isPrimary' 						=> $this->string(36),
			'productId' 						=> $this->string(36)->notNull(),
			'originalURL' 					=> $this->string(255),
			'thumbnailURL' 					=> $this->string(255),
			'status'                => $this->string(50)->defaultValue(ProductVariantImages::STATUS_ACTIVE),
			'createdAt'             => $this->dateTime(),
			'updatedAt'             => $this->dateTime(),
		], $this->tableOptions);

		$this->addPrimaryKey('productImagesId', ProductImages::tableName(), ['id']);
		$this->addForeignKey('fk_product_product_images',
			ProductImages::tableName(), 'productId',
			Product::tableName(), 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(ProductImages::tableName());
	}
}
