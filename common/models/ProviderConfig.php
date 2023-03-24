<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 3/26/2018
 * Time: 4:48 PM
 */

namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProviderConfig
 *
 * @package common\models
 *
 * @property string $id
 * @property string $providerId
 * @property string $group
 * @property string $key
 * @property string $value
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class ProviderConfig extends ActiveRecord
{
    const GROUP_HEADER        = 'header';
    const GROUP_DATA          = 'data';
    const GROUP_QUERY         = 'query';
    const GROUP_JSON_BODY     = 'jsonBody';
    const GROUP_AUTHORIZATION = 'authorization';
    const GROUP_ATTRIBUTE_KEY = 'serviceKey';
    const GROUP_RESPONSE_KEY  = 'responseKey';

    const ATTRIBUTE_BODY_PARAMS     = 'bodyParams';
    const ATTRIBUTE_KEY_LOGS        = 'logs';
    const ATTRIBUTE_KEY_AUTH_METHOD = 'authMethod';

    const RESPONSE_KEY_MESSAGE = 'responseMessage';
    const RESPONSE_KEY_ID      = 'responseId';
    const RESPONSE_KEY_BALANCE = 'responseBalance';

    public static function tableName()
    {
        return '{{%provider_config}}';
    }

    public static function groups()
    {
        return [
            self::GROUP_HEADER        => \Yii::t('app', 'Header'),
            self::GROUP_DATA          => \Yii::t('app', 'Data'),
            self::GROUP_QUERY         => \Yii::t('app', 'Query'),
            self::GROUP_JSON_BODY     => \Yii::t('app', 'Json Body'),
            self::GROUP_AUTHORIZATION => \Yii::t('app', 'Authorization'),
            self::GROUP_ATTRIBUTE_KEY => \Yii::t('app', 'Attribute Key'),
            self::GROUP_RESPONSE_KEY  => \Yii::t('app', 'Response Key'),
        ];
    }

    public static function responseKeys()
    {
        return [
            self::RESPONSE_KEY_MESSAGE => \Yii::t('app', 'Message'),
            self::RESPONSE_KEY_ID      => \Yii::t('app', 'ID'),
            self::RESPONSE_KEY_BALANCE => \Yii::t('app', 'Balance'),
        ];
    }

    public function getData()
    {
        return [$this->key, $this->value];
    }
}