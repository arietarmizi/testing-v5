<?php

use yii\db\Migration;

/**
 * Class m211217_022846_product_bundle_detail_add_column_status
 */
class m211217_022846_product_bundle_detail_add_column_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product_bundle_detail', 'status', $this->string(50)->defaultValue(\common\models\ProductBundleDetail::STATUS_ACTIVE)->after('quantity'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('status', 'string');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_022846_product_bundle_detail_add_column_status cannot be reverted.\n";

        return false;
    }
    */
}
