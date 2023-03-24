<?php


namespace common\models;


use common\base\ActiveRecord;
use nadzif\file\models\File;
use yii\helpers\ArrayHelper;

/**
 * Class ProductGalleries
 * @package common\models
 * @property string  $id
 * @property string  $productId
 * @property string  $marketplacePicId
 * @property string  $type
 * @property string  $fileId
 * @property boolean $isPrimary
 * @property string  $originalURL
 * @property string  $thumbnailURL
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property File    $file
 * @property Product $product
 */
class ProductImages extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED  = 'deleted';

    const TYPE_PRODUCT_IMAGE = 'image';

    public static function tableName()
    {
        return '{{%product_images}}';
    }

    public static function imageFolders()
    {
        return [
            self::TYPE_PRODUCT_IMAGE => 'image',
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
            self::STATUS_DELETED  => \Yii::t('app', 'Deleted'),
        ];
    }

    public function types()
    {
        return [
            self::TYPE_PRODUCT_IMAGE => \Yii::t('app', 'Product Images'),
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function attributeLabels()
    {
        return [];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getImageUrl()
    {
//        $apiBaseUrl = ArrayHelper::getValue('api.baseUrl', Yii::$app->urlManager->baseUrl);
        $apiBaseUrl = ArrayHelper::getValue(\Yii::$app->params, 'api.baseUrl', \Yii::$app->urlManager->baseUrl);
        return $this->file->getThumbnailSource($apiBaseUrl) ? $this->file->getThumbnailSource($apiBaseUrl) : null;
    }


}