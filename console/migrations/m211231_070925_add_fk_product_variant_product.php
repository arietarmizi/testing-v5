<?php

use common\models\Product;
use common\models\ProductVariant;
use console\base\Migration;

/**
 * Class m211231_070925_add_fk_product_variant_product
 */
class m211231_070925_add_fk_product_variant_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addForeignKey(
    		'fk_product_variant_product',
			ProductVariant::tableName(), 'productId',
			Product::tableName(), 'id',
			'CASCADE','CASCADE'
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_variant_product', ProductVariant::tableName());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211231_070925_add_fk_product_variant_product cannot be reverted.\n";

        return false;
    }
    */
}
