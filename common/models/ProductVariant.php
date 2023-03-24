<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Variant
 * @package common\models
 *
 * @property string  $id
 * @property string  $shopId
 * @property string  $sku
 * @property string  $productId
 * @property string  $marketplaceProductVariantId
 * @property string  $name
 * @property string  $sellingStatus
 * @property boolean $isShelfLife
 * @property string  $duration
 * @property string  $inboundLimit
 * @property string  $outboundLimit
 * @property string  $minOrder
 * @property string  $description
 * @property string  $productDescription
 * @property double  $defaultPrice
 * @property double  $length
 * @property double  $width
 * @property double  $height
 * @property double  $weight
 * @property string  $barcode
 * @property double  $discount
 * @property boolean $isPreOrder
 * @property double  $minPreOrderDay
 * @property string  $productInformationId
 * @property string  $productCostInformationId
 * @property string  $productShipmentId
 * @property boolean $isProductCombination
 * @property string  $productBundlingId
 * @property string  $productImageId
 * @property string  $productStockId
 * @property string  $warehouseId
 * @property boolean $isWholesale
 * @property string  $wholeSaleId
 * @property boolean $isFreeReturn
 * @property boolean $isMustInsurance
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property ProductVariantImages[] $images
 * @property Product $product
 * @property StockManagement $stock
 */
class ProductVariant extends ActiveRecord
{
    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETED  = 2;

    const CONDITION_NEW    = 1;
    const CONDITION_SECOND = 0;

    const SELLING_FOR_SALE     = 'for sale';
    const SELLING_SOLD_OUT     = 'sold out';
    const SELLING_NEW_PRODUCT  = 'new product';
    const SELLING_BIG_SALE     = 'big sale';
    const SELLING_DISCONTINUED = 'discontinued';

    public static function sellingTypes()
    {
        return [
            self::SELLING_FOR_SALE     => \Yii::t('app', 'For Sale'),
            self::SELLING_SOLD_OUT     => \Yii::t('app', 'Sold Out'),
            self::SELLING_NEW_PRODUCT  => \Yii::t('app', 'New Product'),
            self::SELLING_BIG_SALE     => \Yii::t('app', 'Big Sale'),
            self::SELLING_DISCONTINUED => \Yii::t('app', 'Discontinued'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }

    public static function conditions()
    {
        return [
            self::CONDITION_NEW    => \Yii::t('app', 'New'),
            self::CONDITION_SECOND => \Yii::t('app', 'Second')
        ];
    }

    public static function tableName()
    {
        return '{{%product_variant}}';
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    public function getImages()
    {
    	return $this->hasMany(ProductVariantImages::class, ['productVariantId' => 'id']);
    }

    public function getStock(){
    	return $this->hasOne(StockManagement::className(), ['productVariantId' => 'id']);
	}

}