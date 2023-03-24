<?php


namespace common\models;


use Carbon\Carbon;
use common\base\ActiveRecord;

/**
 * Class Subscription
 * @package common\models
 * @property string $id
 * @property string $subscriptionTypeId
 * @property string $userId
 * @property string $registerAt
 * @property string $expiredAt
 * @property string $usedQuota
 * @property double $remainingQuota
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class Subscription extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive'),
        ];
    }

    public static function tableName()
    {
        return '{{%subscription}}';
    }

    public function getSubscriptionType()
    {
//        return $this->hasOne(Subscription::class, ['id' => 'subscriptionTypeId']);
        return $this->hasOne(SubscriptionType::class, ['id' => 'subscriptionTypeId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function countSubscription()
    {
        $query = SubscriptionType::find()
            ->where(['id' => $this->subscriptionTypeId])
            ->one();

        if ($query->durationType == SubscriptionType::DURATION_YEAR) {
            $this->registerAt     = Carbon::now();
            $this->expiredAt      = Carbon::now()->addYear($query->duration);
            $this->remainingQuota = $query->transactionQuota;
        } else if ($query->durationType == SubscriptionType::DURATION_MONTH) {
            $this->registerAt     = Carbon::now();
            $this->expiredAt      = Carbon::now()->addMonth($query->duration);
            $this->remainingQuota = $query->transactionQuota;
        } else if ($query->durationType == SubscriptionType::DURATION_WEEK) {
            $this->registerAt     = Carbon::now();
            $this->expiredAt      = Carbon::now()->addWeek($query->duration);
            $this->remainingQuota = $query->transactionQuota;
        } else if ($query->durationType == SubscriptionType::DURATION_DAY) {
            $this->registerAt     = Carbon::now();
            $this->expiredAt      = Carbon::now()->addDay($query->duration);
            $this->remainingQuota = $query->transactionQuota;
        }
    }
}