<?php

use console\base\Migration;
use common\models\CourierInformation;

/**
 * Handles the creation of table `{{%courier_information}}`.
 */
class m211125_034708_create_courier_information_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CourierInformation::tableName(), [
            'id'                   => $this->string(36)->notNull(),
            'marketplaceCourierId' => $this->string(100),
            'courierName'          => $this->string(255),
            'phoneNumber'          => $this->double(53),
            'notes'                => $this->text(),
            'status'               => $this->string(50)->defaultValue(CourierInformation::STATUS_ACTIVE),
            'createdAt'            => $this->dateTime(),
            'updatedAt'            => $this->dateTime(),
        ], $this->tableOptions);
        $this->addPrimaryKey('courierId', CourierInformation::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CourierInformation::tableName());
    }
}
