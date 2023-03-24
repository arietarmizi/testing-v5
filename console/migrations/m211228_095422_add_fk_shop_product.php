<?php

use common\models\Product;
use common\models\Shop;
use yii\db\Migration;

/**
 * Class m211228_095422_add_fk_shop_product
 */
class m211228_095422_add_fk_shop_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
			$this->addForeignKey(
				"fk_shop_product",
				Product::tableName(),
				'shopId',
				Shop::tableName(),
				'id',
				'CASCADE',
				'CASCADE'
			);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_shop_product',Product::tableName());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_095422_add_fk_shop_product cannot be reverted.\n";

        return false;
    }
    */
}
