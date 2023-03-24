<?php

use console\base\Migration;
use common\models\Province;

/**
 * Handles the creation of table `{{%province}}`.
 */
class m200529_121010_create_province_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Province::tableName(), [
            'id'          => $this->string(36)->notNull(),
                        
            'code'        => $this->string(50)->notNull()->unique(),
            'name'        => $this->string(255)->notNull(),
            'description' => $this->text(), 

            'status'      => $this->string(50)->defaultValue(Province::STATUS_ACTIVE),
            'createdAt'   => $this->dateTime(),
            'updatedAt'   => $this->dateTime()
        ], $this->tableOptions);

        $this->addPrimaryKey('provinceId', Province::tableName(), ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Province::tableName());
    }
}
