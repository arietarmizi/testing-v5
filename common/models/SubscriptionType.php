<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class SubscriptionType
 * @package common\models
 *
 * @property string  $id
 * @property string  $name
 * @property integer $duration
 * @property string  $durationType
 * @property boolean $isSupportMultiple
 * @property string  $transactionQuota
 * @property string  $price
 * @property string  $description
 * @property integer $priority
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 */
class SubscriptionType extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS_DELETED = 'deleted';

    const DURATION_YEAR  = 'year';
    const DURATION_MONTH = 'month';
    const DURATION_WEEK  = 'week';
    const DURATION_DAY   = 'day';

    public static function durations()
    {
        return [
            self::DURATION_YEAR  => \Yii::t('app', 'Year'),
            self::DURATION_MONTH => \Yii::t('app', 'Month'),
            self::DURATION_WEEK  => \Yii::t('app', 'Week'),
            self::DURATION_DAY   => \Yii::t('app', 'Day'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive'),
        ];
    }

    public static function deleted()
    {
        return [
            self::STATUS_DELETED => \Yii::t('app', 'Deleted')
        ];
    }

    public static function tableName()
    {
        return '{{%subscription_type}}';
    }

    public function isActive()
    {
        return $this->status = self::STATUS_ACTIVE;
    }

    public function getSubscription()
    {
        return $this->hasMany(Subscription::class, ['subscriptionTypeId' => 'id']);
    }

}