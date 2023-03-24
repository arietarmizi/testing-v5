<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductPromo
 * @package common\models
 * @property string  $id
 * @property string  $productVariantId
 * @property integer $minQuantity
 * @property integer $maxQuantity
 * @property double  $defaultPrice
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 */
class ProductPromo extends ActiveRecord
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
        return '{{%product_promo}}';
    }
}