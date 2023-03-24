<?php

use common\models\Provider;

/**
 * Handles the creation of table `provider`.
 */
class m180425_124938_create_provider_table extends \console\base\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Provider::tableName(), [
            'id'               => $this->string(36)->notNull()->unsigned(),
            'name'             => $this->string(255)->notNull(),
            'type'             => $this->string(50)->notNull()->defaultValue(Provider::TYPE_TOKOPEDIA),
            'host'             => $this->string(255)->notNull(),
            'proxy'            => $this->string(255),
            'authUrl'          => $this->string(255),
            'authMethod'       => $this->string(50)->null(),
            'token'            => $this->string(255)->null(),
            'tokenExpiredIn'   => $this->integer(),
            'tokenExpiredAt'   => $this->dateTime(),
            'requestMethod'    => $this->string(50)->notNull()->defaultValue(Provider::REQUEST_METHOD_POST),
            'requestBody'      => $this->string(50)->null(),
            'requestTimeout'   => $this->integer()->defaultValue(Provider::DEFAULT_REQUEST_TIMEOUT),
            'responseLanguage' => $this->string(50)->notNull(),
            'status'           => $this->string(50)->defaultValue(Provider::STATUS_ACTIVE),
            'createdAt'        => $this->dateTime(),
            'updatedAt'        => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('providerId', Provider::tableName(), ['id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Provider::tableName());
    }
}
