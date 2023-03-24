<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Warehouse
 * @package common\models
 *
 * @property string   $id
 * @property string   $shopId
 * @property string   $marketplaceWarehouseId
 * @property string   $districtId
 * @property string   $cityId
 * @property string   $provinceId
 * @property string   $name
 * @property string   $email
 * @property string   $address
 * @property string   $postalCode
 * @property string   $latitude
 * @property string   $longitude
 * @property boolean  $isDefault
 * @property string   $status
 * @property string   $createdAt
 * @property string   $updatedAt
 *
 * @property Shop     $shop
 * @property District $district
 * @property City     $city
 * @property Province $province
 */
class Warehouse extends ActiveRecord {
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    public static function tableName() {
        return '{{%warehouse}}';
    }

    public static function statuses() {
        return [
            self:: STATUS_ACTIVE  => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }
}