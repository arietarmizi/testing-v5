<?php


namespace common\models;


use common\base\ActiveRecord;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

/**
 * Class Product
 * @package common\models
 *
 * @property string           $id
 * @property string           $marketplaceProductId
 * @property string           $marketplaceId
 * @property string           $productId
 * @property string           $shopId
 * @property string           $productCategoryId
 * @property String           $categoryBreadcrumb
 * @property string           $sku
 * @property string           $code
 * @property string           $name
 * @property string           $condition
 * @property string           $minOrder
 * @property string           $defaultPrice
 * @property string           $stock
 * @property string           $productDescription
 * @property string           $description
 * @property boolean          $isMaster
 * @property string           $status
 * @property string           $createdAt
 * @property string           $updatedAt
 *
 * @property ProductVariant[] $productVariants
 * @property ProductImages[]  $productImages
 * @property ProductCategory  $productCategory
 * @property double           $minPrice
 * @property double           $maxPrice
 * @property int              $totalStock
 */
class Product extends ActiveRecord {
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';
    const STATUS_OUT_OF_STOCK = 'out of stock';
    const STATUS_DELETED = 'deleted';

    const CONDITION_NEW = 'new';
    const CONDITION_SECOND = 'second';

    public $_categoryBreadcrumb;

    public static function tableName() {
        return '{{%product}}';
    }

    public static function statuses() {
        return [
            self::STATUS_ACTIVE       => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE     => \Yii::t('app', 'Inactive'),
            self::STATUS_OUT_OF_STOCK => \Yii::t('app', 'Out Of Stock'),
            self::STATUS_DELETED      => \Yii::t('app', 'Deleted')
        ];
    }

    public static function conditions() {
        return [
            self::CONDITION_NEW    => \Yii::t('app', 'New'),
            self::CONDITION_SECOND => \Yii::t('app', 'Second'),
        ];
    }

    public function getProductCategory() {
        return $this->hasOne(ProductCategory::class, ['id' => 'productCategoryId']);
    }

    public function getCategoryBreadcrumb($counter = 0, $categoryBreadcrumb = '') {
        $parentId = $this->productCategoryId;


        /** @var ProductCategory $category */
        $category = ProductCategory::find()
            ->where(['id' => $parentId])
            ->one();

        if ($counter == 0) {
            $categoryBreadcrumb = $category->name;
        } else {
            $categoryBreadcrumb = $category->name . " > " . $categoryBreadcrumb;
        }

        $this->_categoryBreadcrumb = $categoryBreadcrumb;
        if ($category->parentId != null) {
            $counter++;
            $this->productCategoryId = $category->parentId;
            $this->getCategoryBreadcrumb($counter, $categoryBreadcrumb);
        }

        return $this->_categoryBreadcrumb;

    }

    public function getShop() {
        return $this->hasOne(Shop::class, ['marketplaceShopId' => 'shopId']);
    }

    public function getProductImages() {
        return $this->hasMany(ProductImages::class, ['ProductId' => 'id'])
            ->orderBy(['isPrimary' => SORT_DESC]);
    }

    public function getProductVariants() {
        return $this->hasMany(ProductVariant::class, ['productId' => 'id']);
    }

    public function getMinPrice() {
        return ProductVariant::find()
            ->where([ProductVariant::tableName() . '.productId' => $this->id])
            ->min(ProductVariant::tableName() . '.defaultPrice');
    }

    public function getMaxPrice() {
        return ProductVariant::find()
            ->where([ProductVariant::tableName() . '.productId' => $this->id])
            ->max(ProductVariant::tableName() . '.defaultPrice');
    }

    public function getTotalStock() {
        return StockManagement::find()
            ->joinWith(['productVariant'])
            ->where([ProductVariant::tableName() . '.productId' => $this->id])
            ->sum(StockManagement::tableName() . '.availableStock');
    }
}