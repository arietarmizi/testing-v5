<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class OrderDetail
 * @package common\models
 * @property string         $id
 * @property string         $orderId
 * @property string         $productVariantId
 * @property double         $quantity
 * @property double         $weight
 * @property double         $height
 * @property double         $totalWeight
 * @property boolean        $isFreeReturn
 * @property double         $productPrice
 * @property double         $insurancePrice
 * @property double         $subTotalPrice
 * @property string         $notes
 * @property string         $createdAt
 * @property string         $updatedAt
 *
 * @property Order          $order
 * @property ProductVariant $productVariant
 */
class OrderDetail extends ActiveRecord {
    public static function tableName() {
        return '{{%order_detail}}';
    }

    public function getProductVariant() {
        return $this->hasOne(ProductVariant::class, ['id' => 'productVariantId']);
    }

    public function getOrder() {
        return $this->hasOne(Order::class, ['id' => 'orderId']);
    }
}