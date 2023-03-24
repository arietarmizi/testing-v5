<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ShipmentService
 * @package common\models
 *
 * @property string   $id
 * @property string   $shipmentId
 * @property string   $marketplaceShipmentServiceId
 * @property string   $name
 * @property string   $isAvailable
 * @property string   $status
 * @property string   $createdAt
 * @property string   $updatedAt
 *
 * @property Shipment $shipment
 */
class ShipmentService extends ActiveRecord {
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    const IS_AVAILABLE_TRUE = true;
    const IS_AVAILABLE_FALSE = false;

    public static function tableName() {
        return '{{%shipment_service}}';
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

    public function getShipment() {
        return $this->hasOne(Shipment::class, ['id' => 'shipmentId']);
    }
}