<?php


namespace common\models;
use common\base\ActiveRecord;
use nadzif\file\models\File;

/**
 * Class ProductGalleries
 * @package common\models
 * @property string  $id
 * @property string  $productVariantId
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
 * @property ProductVariant $productVariant
 */
class ProductVariantImages extends ActiveRecord
{

	const STATUS_ACTIVE   = 'active';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_DELETED  = 'deleted';

	public static function tableName()
	{
		return '{{%product_variant_images}}';
	}

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		return $behaviors;
	}

	public static function statuses()
	{
		return [
			self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
			self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
			self::STATUS_DELETED  => \Yii::t('app', 'Deleted'),
		];
	}

	public function getProductVariant()
	{
		return $this->hasOne(ProductVariant::class, ['id' => 'productVariantId']);
	}
}