<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class OrderStatus
 * @package common\models
 *
 * @property string      $id
 * @property string      $marketplaceId
 * @property string      $marketplaceStatusCode
 * @property string      $description
 * @property string      $status
 * @property string      $createdAt
 * @property string      $updatedAt
 *
 * @property Marketplace $marketplace
 */
class OrderStatus extends ActiveRecord {
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    public static function tableName() {
        return '{{%order_status}}';
    }

    public static function statuses() {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
            self::STATUS_DELETED  => \Yii::t('app', 'Deleted'),
        ];
    }

    public function getMarketplace() {
        return $this->hasOne(Marketplace::class, ['id' => 'marketplaceId']);
    }
}