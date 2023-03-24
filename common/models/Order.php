<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Order
 * @package common\models
 * @property string          $id
 * @property string          $orderId
 * @property string          $orderDate
 * @property string          $shopId
 * @property string          $refInv
 * @property string          $customerId
 * @property string          $shipmentId
 * @property string          $shipmentServiceId
 * @property string          $warehouseId
 * @property string          $promoId
 * @property string          $discountId
 * @property string          $orderStatusId
 * @property integer          $total
 * @property string          $createdAt
 * @property string          $updatedAt
 *
 * @property Customer        $customer
 * @property Shipment        $shipment
 * @property ShipmentService $shipmentService
 * @property Warehouse       $warehouse
 * @property ProductPromo    $productPromo
 * @property ProductDiscount $productDiscount
 * @property OrderDetail[]   $orderDetails
 * @property OrderStatus     $orderStatus
 */
class Order extends ActiveRecord {
    const ORDER_STATUS_PACKING = 'packing';
    const ORDER_STATUS_SUCCESS = 'success';
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_CANCELLED = 'cancelled';
    const ORDER_STATUS_FAILED = 'failed';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    public static function orderStatuses() {
        return [
            self::ORDER_STATUS_PACKING   => \Yii::t('app', 'Packing'),
            self::ORDER_STATUS_SUCCESS   => \Yii::t('app', 'Success'),
            self::ORDER_STATUS_PENDING   => \Yii::t('app', 'Pending'),
            self::ORDER_STATUS_CANCELLED => \Yii::t('app', 'Cancelled'),
            self::ORDER_STATUS_FAILED    => \Yii::t('app', 'Failed')
        ];
    }

    public static function statuses() {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function tableName() {
        return '{{%order}}';
    }

    public function getCustomer() {
        return $this->hasOne(Customer::class, ['id' => 'customerId']);
    }

    public function getShipment() {
        return $this->hasOne(Shipment::class, ['id' => 'shipmentId']);
    }

    public function getShipmentService() {
        return $this->hasOne(ShipmentService::class, ['id' => 'shipmentServiceId']);
    }

    public function getWarehouse() {
        return $this->hasOne(Warehouse::class, ['id' => 'warehouseId']);
    }

    public function getProductPromo() {
        return $this->hasOne(ProductPromo::class, ['id' => 'promoId']);
    }

    public function getProductDiscount() {
        return $this->hasOne(ProductDiscount::class, ['id' => 'discountId']);
    }

    public function getOrderDetails() {
        return $this->hasMany(OrderDetail::class, ['orderId' => 'id']);
    }

    public function getOrderStatus() {
        return $this->hasOne(OrderStatus::class, ['id' => 'orderStatusId']);
    }

	public function getTotal() {
		return OrderDetail::find()
			->where([OrderDetail::tableName() . '.orderId' => $this->id])
			->sum(OrderDetail::tableName() . '.subTotalPrice');
	}
}