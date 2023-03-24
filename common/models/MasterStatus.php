<?php


namespace common\models;


use common\base\ActiveRecord;


/**
 * Class MasterStatus
 * @package common\models
 * @property string $marketplaceId
 * @property string $statusCode
 * @property string $desc
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class MasterStatus extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED  = 'deleted';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive'),
        ];
    }

    public static function deletedStatus()
    {
        return [
            self::STATUS_DELETED => \Yii::t('app', 'Deleted'),
        ];
    }

    public static function tableName()
    {
        return '{{%master_status}}';
    }

    public function getMarketplace()
    {
        return $this->hasOne(Marketplace::class, ['id' => 'marketplaceId']);
    }

}