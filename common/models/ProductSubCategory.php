<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductCategoryDetail
 * @package common\models
 *
 * @property string $id
 * @property string $productCategoryId
 * @property string $name
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class ProductSubCategory extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function tableName()
    {
        return '{{%product_sub_category}}';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['uuid']);
        return $behaviors;
    }

    public function getProductCategory()
    {
        return $this->hasOne(ProductCategory::class, ['productCategoryId' => 'id']);
    }
}