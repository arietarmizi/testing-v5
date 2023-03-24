<?php

use common\models\ProductVariantImages;
use console\base\Migration;

/**
 * Class m211231_070037_add_column_product_variant_images
 */
class m211231_070037_add_column_product_variant_images extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn(ProductVariantImages::tableName(),'marketplacePicId',$this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn(ProductVariantImages::tableName(),'marketplacePicId');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211231_070037_add_column_product_variant_images cannot be reverted.\n";

        return false;
    }
    */
}
