<?php

use console\base\Migration;
use common\models\District;
use common\models\City;

/**
 * Handles the creation of table `{{%district}}`.
 */
class m200529_123010_create_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(District::tableName(), [
            'id' => $this->string(36)->notNull(),

            'cityId'      => $this->string(36)->notNull(),
            'name'        => $this->string(255)->notNull(),
            'description' => $this->text(),

            'status'    => $this->string(50)->defaultValue(District::STATUS_ACTIVE),
            'createdAt' => $this->dateTime(),
            'updatedAt' => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('districtId', District::tableName(), ['id']);

//        $this->setForeignKey(District::tableName(), 'cityId', City::tableName(), 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey(
            $this->formatForeignKeyName(District::tableName(), City::tableName()),
            District::tableName(), 'cityId',
            City::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(District::tableName());
    }
}
