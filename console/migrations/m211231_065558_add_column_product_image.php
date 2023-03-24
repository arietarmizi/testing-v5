<?php

use common\models\ProductImages;
use console\base\Migration;

/**
 * Class m211231_065558_add_column_product_image
 */
class m211231_065558_add_column_product_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn(ProductImages::tableName(),'marketplacePicId',$this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(ProductImages::tableName(),'marketplacePicId');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211231_065558_add_column_product_image cannot be reverted.\n";

        return false;
    }
    */
}
