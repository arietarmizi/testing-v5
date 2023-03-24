<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductDiscount
 * @package common\models
 * @property string  $id
 * @property string  $productVariantId
 * @property double  $discountPrice
 * @property double  $discountPercentage
 * @property string  $startTime
 * @property string  $endTime
 * @property double  $initialQuota
 * @property double  $remainingQuota
 * @property double  $maxOrder
 * @property string  $slashPriceStatusId
 * @property boolean $useWarehouse
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 */
class ProductDiscount extends ActiveRecord
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

    public static function tableName()
    {
        return '{{%product_discount}}';
    }

    public function getProductVariant()
    {
        return $this->hasOne(ProductVariant::class, ['id' => 'productVariantId']);
    }
}