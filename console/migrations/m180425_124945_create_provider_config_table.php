<?php

use common\models\Provider;
use common\models\ProviderConfig;
use console\base\Migration;

/**
 * Handles the creation of table `provider_config`.
 */
class m180425_124945_create_provider_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ProviderConfig::tableName(), [
            'id'         => $this->string(36)->unsigned()->notNull(),
            'providerId' => $this->string(36)->unsigned()->notNull(),
            'group'      => $this->string(50)->notNull(),
            'key'        => $this->string(100)->notNull(),
            'value'      => $this->string(100)->notNull(),
            'createdAt'  => $this->dateTime(),
            'updatedAt'  => $this->dateTime(),
        ], $this->tableOptions);


        $this->addPrimaryKey('providerConfigId', ProviderConfig::tableName(), ['id']);

        $this->addForeignKey(
            $this->formatForeignKeyName(ProviderConfig::tableName(), Provider::tableName()),
            ProviderConfig::tableName(), 'providerId',
            Provider::tableName(), 'id',
            'CASCADE', 'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ProviderConfig::tableName());
    }
}
