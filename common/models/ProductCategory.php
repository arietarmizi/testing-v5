<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductCategory
 * @package common\models
 *
 * @property string $id
 * @property string $name
 * @property string $parentId
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class ProductCategory extends ActiveRecord
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
        return '{{%product_category}}';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['uuid']);
        return $behaviors;
    }

    public function getProductSubCategory()
    {
//        return $this->hasOne(ProductSubCategory::class, ['productCategoryId' => 'id']);
        return $this->hasOne(ProductSubCategory::class, ['productCategoryId' => 'id']);
    }

    public function getChild(){
    	return ProductCategory::find()
				->where([ProductCategory::tableName().'.parentId' => $this->id])
				->all();
		}
}