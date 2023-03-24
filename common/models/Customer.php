<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Customer
 * @package common\models
 * @property string $id
 * @property string $marketplaceCustomerId
 * @property string $customerName
 * @property string $email
 * @property double $phoneNumber
 * @property string $address
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class Customer extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED  = 'deleted';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }

    public static function tableName()
    {
        return '{{%customer}}';
    }
}