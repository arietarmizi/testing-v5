<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductBundleDetail
 * @package common\models
 *
 * @property string $id
 * @property string $productBundleId
 * @property string $productVariantId
 * @property double $quantity
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class ProductBundleDetail extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    consT STATUS_DELETED  = 'deleted';

    public static function statuses()
    {
        return [
            self:: STATUS_ACTIVE  => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }

    public static function tableName()
    {
        return '{{%product_bundle_detail}}';
    }

    public function getProductBundle()
    {
        return $this->hasOne(ProductBundle::class, ['id' => 'productBundleId']);
    }

    public function getProductVariant()
    {
        return $this->hasMany(ProductVariant::class, ['id' => 'productVariantId']);
    }

}