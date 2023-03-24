<?php

use console\base\Migration;
use common\models\SubDistrict;
use common\models\District;

/**
 * Handles the creation of table `{{%sub_district}}`.
 */
class m200529_124010_create_sub_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(SubDistrict::tableName(), [
            'id' => $this->string(36)->notNull(),

            'districtId'  => $this->string(36)->notNull(),
            'name'        => $this->string(255)->notNull(),
            'description' => $this->text(),
            'postalCode'  => $this->string(5),

            'status'    => $this->string(50)->defaultValue(SubDistrict::STATUS_ACTIVE),
            'createdAt' => $this->dateTime(),
            'updatedAt' => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('subDistrictId', SubDistrict::tableName(), ['id']);

//        $this->setForeignKey(SubDistrict::tableName(), 'districtId', District::tableName(), 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey(
            $this->formatForeignKeyName(SubDistrict::tableName(), District::tableName()),
            SubDistrict::tableName(), 'districtId',
            District::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(SubDistrict::tableName());
    }
}
