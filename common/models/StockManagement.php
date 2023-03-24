<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductStock
 * @package common\models
 *
 * @property string         $id
 * @property string         $productVariantId
 * @property double         $promotionStock
 * @property double         $orderedStock
 * @property double         $availableStock
 * @property double         $onHandStock
 * @property double         $stockAlert
 * @property string         $warehouseId
 * @property string         $stockType
 * @property string         $status
 * @property string         $createdAt
 * @property string         $updatedAt
 *
 * @property ProductVariant $productVariant
 * @property Warehouse      $warehouse
 */
class StockManagement extends ActiveRecord {
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    const STOCK_IN = 'in';
    const STOCK_OUT = 'out';

    public static function stocks() {
        return [
            self::STOCK_IN  => \Yii::t('app', 'Stock In'),
            self::STOCK_OUT => \Yii::t('app', 'Stock Out'),
        ];
    }

    public static function statuses() {
        return [
            self:: STATUS_ACTIVE  => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }

    public static function tableName() {
        return '{{%stock_management}}';
    }

    public function isActive() {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function getProductVariant() {
        return $this->hasOne(ProductVariant::class, ['id' => 'productVariantId']);
    }

    public function getWarehouse() {
        return $this->hasOne(Warehouse::class, ['id' => 'warehouseId']);
    }
}