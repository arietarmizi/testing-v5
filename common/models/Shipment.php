<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Shipment
 * @package common\models
 *
 * @property string          $id
 * @property string          $shopId
 * @property string          $marketplaceShipmentId
 * @property string          $name
 * @property boolean         $isAvailable
 * @property string          $status
 * @property string          $createdAt
 * @property string          $updatedAt
 *
 * @property Shop            $shop
 * @property ShipmentService $shipmentServices
 */
class Shipment extends ActiveRecord {
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    const IS_AVAILABLE_TRUE = true;
    const IS_AVAILABLE_FALSE = false;

    public static function tableName() {
        return '{{%shipment}}';
    }

    public static function statuses() {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
            self::STATUS_DELETED  => \Yii::t('app', 'Deleted'),
        ];
    }

    public static function available() {
        return [
            self::IS_AVAILABLE_TRUE  => \Yii::t('app', 'Yes'),
            self::IS_AVAILABLE_FALSE => \Yii::t('app', 'No')
        ];
    }

    public function getShop() {
        return $this->hasOne(Shop::class, ['id' => 'shopId']);
    }

    public function getShipmentServices() {
        return $this->hasMany(ShipmentService::class, ['shipmentId' => 'id']);
    }
}