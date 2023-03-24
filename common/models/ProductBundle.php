<?php


namespace common\models;


use common\base\ActiveRecord;

/**
 * Class ProductBundle
 * @package common\models
 *
 * @property $id
 * @property $name
 * @property $price
 * @property $description
 * @property $status
 * @property $createdAt
 * @property $updatedAt
 */
class ProductBundle extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED   = 'deleted';

    public static function statuses()
    {
        return [
            self:: STATUS_ACTIVE  => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive')
        ];
    }

    public static function tableName()
    {
        return '{{%product_bundle}}';
    }
}