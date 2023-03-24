<?php

use common\models\Shop;
use common\models\Warehouse;
use console\base\Migration;

/**
 * Handles the creation of table `{{%warehouse}}`.
 */
class m211228_082106_create_warehouse_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable(Warehouse::tableName(), [
            'id'                     => $this->string(36)->notNull(),
            'shopId'                 => $this->string(36)->notNull(),
            'marketplaceWarehouseId' => $this->string(36)->notNull(),
            'districtId'             => $this->string(36),
            'cityId'                 => $this->string(36),
            'provinceId'             => $this->string(36),
            'name'                   => $this->string(100),
            'email'                  => $this->string(100),
            'address'                => $this->text(),
            'postalCode'             => $this->string(10),
            'latitude'               => $this->float(),
            'longitude'              => $this->float(),
            'isDefault'              => $this->boolean()->defaultValue(0),
            'status'                 => $this->string(36)->defaultValue(Warehouse::STATUS_ACTIVE),
            'createdAt'              => $this->dateTime(),
            'updatedAt'              => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('pk_warehouse_id', Warehouse::tableName(), ['id']);
        $this->addForeignKey('fk_warehouse_shop',
            Warehouse::tableName(), 'shopId',
            Shop::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable(Warehouse::tableName());
    }
}
