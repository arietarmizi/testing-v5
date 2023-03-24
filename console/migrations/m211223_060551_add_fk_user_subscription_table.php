<?php

use common\models\Subscription;
use common\models\User;
use yii\db\Migration;

/**
 * Class m211223_060551_add_fk_user_subscription_table
 */
class m211223_060551_add_fk_user_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
			$this->addForeignKey('fk_user_subscription', Subscription::tableName(),
				'userId', User::tableName(), 'id',
				'CASCADE', 'CASCADE'
			);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_060551_add_fk_user_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211223_060551_add_fk_user_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
