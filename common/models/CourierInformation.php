<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class CourierInformation
 * @package common\models
 * @property string $id
 * @property string $marketplaceCourierId
 * @property string $courierName
 * @property string $phoneNumber
 * @property string $notes
 * @property string $status
 */
class CourierInformation extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED  = 'deleted';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function tableName()
    {
        return '{{%courier_information}}';
    }
}