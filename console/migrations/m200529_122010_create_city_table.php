<?php

use console\base\Migration;
use common\models\City;
use common\models\Province;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m200529_122010_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(City::tableName(), [
            'id' => $this->string(36)->notNull(),

            'provinceId'  => $this->string(36)->notNull(),
            'code'        => $this->string(50)->notNull()->unique(),
            'name'        => $this->string(255)->notNull(),
            'description' => $this->text(),

            'status'    => $this->string(50)->defaultValue(City::STATUS_ACTIVE),
            'createdAt' => $this->dateTime(),
            'updatedAt' => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('cityId', City::tableName(), ['id']);

//        $this->addForeignKey(City::tableName(), 'provinceId', Province::tableName(), 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey(
            $this->formatForeignKeyName(City::tableName(), Province::tableName()),
            City::tableName(), 'provinceId',
            Province::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(City::tableName());
    }
}
