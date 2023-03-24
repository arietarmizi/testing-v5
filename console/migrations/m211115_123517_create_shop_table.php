<?php

use console\base\Migration;
use common\models\Shop;

/**
 * Handles the creation of table `{{%shop}}`.
 */
class m211115_123517_create_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Shop::tableName(), [
            'id'                => $this->string(36)->notNull(),
            'marketplaceShopId' => $this->string(100)->notNull(),
            'marketplaceId'     => $this->string(36)->notNull(),
            'fsId'              => $this->string(255)->notNull(),
            'userId'            => $this->string(36)->notNull(),
            'shopName'          => $this->string(255)->notNull(),
            'shopLogo'          => $this->text(),
            'shopUrl'           => $this->text(),
            'districtId'        => $this->string(36),
            'description'       => $this->text(),
            'domain'            => $this->string(255),
            'isOpen'            => $this->boolean()->defaultValue(0),
            'status'            => $this->string(50)->defaultValue(Shop::STATUS_ACTIVE),
            'createdAt'         => $this->dateTime(),
            'updatedAt'         => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('shopId', Shop::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Shop::tableName());
    }
}
