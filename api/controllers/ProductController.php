<?php


namespace api\controllers;

use api\actions\ListAction;
use api\actions\ViewAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\product\AddProductImagesForm;
use api\forms\product\CreateProductForm;
use api\forms\product\UpdateProductForm;
use api\forms\tokopedia\product\DownloadProductsForm;
//use api\forms\tokopedia\product\GetAllProductsForm;
use api\forms\tokopedia\product\GetInfoByIdForm;
use api\forms\tokopedia\product\GetInfoBySkuForm;
use api\forms\tokopedia\product\GetProductVariantForm;
use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductImages;
use common\models\ProductSubCategory;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use function foo\func;

class ProductController extends Controller
{
	public function behaviors()
	{
		$behaviors                        = parent::behaviors();
		$behaviors['content-type-filter'] = [
			'class'       => ContentTypeFilter::class,
			'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
			'only'        => [
				'create',
				'update',
				'download'
			]
		];
		return $behaviors;
	}

	public function Actions()
	{
		return [
			'create'    => [
				'class'          => FormAction::className(),
				'formClass'      => CreateProductForm::className(),
				'messageSuccess' => \Yii::t('app', 'Create Product Success.'),
				'messageFailed'  => \Yii::t('app', 'Create Product Failed.'),
				'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
				'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
			],
			'update'    => [
				'class'          => FormAction::class,
				'formClass'      => UpdateProductForm::class,
				'messageSuccess' => \Yii::t('app', 'Update Product success'),
				'messageFailed'  => \Yii::t('app', 'Update Product failed'),
				'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
				'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
				'statusSuccess'  => 200,
				'statusFailed'   => 400,
			],
			'list'      => [
				'class'             => ListAction::class,
				'query'             => function () {
					return Product::find()
                        ->where([Product::tableName() . '.status' => Product::STATUS_ACTIVE]);
				},
                'filters' => [
                    'shopId' => ['=', Product::tableName() . '.shopId']
                ],
				'toArrayProperties' => [
					Product::class => [
						'id',
						'shopId',
						'name',
						'condition',
						'minOrder',
						'productDescription',
						'description',
						'isMaster',
						'minPrice' => function ($model) {
							return $model->minPrice;
						},
						'maxPrice' => function ($model) {
							return $model->maxPrice;
						},
                        'totalStock' => function ($model) {
		                    /** @var Product $model */
                            return (int)$model->totalStock;
                        },
						'productCategory' => function ($model) {
							return ArrayHelper::toArray($model->productCategory, [
								ProductCategory::class => [
									'id',
									'name'
								]
							]);
						},
						'productImages'      => function ($model) {
							return ArrayHelper::toArray($model->productImages, [
								ProductImages::class => [
									'id',
									'originalURL',
									'thumbnailURL',
									'isPrimary'
								]
							]);
						},
						'status'
					]
				],
				'apiCodeSuccess'    => 0,
				'apiCodeFailed'     => 400,
				'successMessage'    => \Yii::t('app', 'Get Product List Success'),
			],
			'add-image' => [
				'class'          => FormAction::class,
				'formClass'      => AddProductImagesForm::class,
				'messageSuccess' => \Yii::t('app', 'Add product image success'),
				'messageFailed'  => \Yii::t('app', 'Add product image failed'),
				'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
				'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
				'statusSuccess'  => 200,
				'statusFailed'   => 400,
			],
			'download'    => [
				'class'          => FormAction::className(),
				'formClass'      => DownloadProductsForm::className(),
				'messageSuccess' => \Yii::t('app', 'Download Product Success.'),
				'messageFailed'  => \Yii::t('app', 'Download Product Failed.'),
				'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
				'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
			],
			'detail'   => [
				'class'             => ViewAction::class,
				'query'             => function () {
					return Product::find();
				},
				'toArrayProperties' => [
					Product::class => [
						'id',
						'shopId',
						'name',
						'condition',
						'minOrder',
						'productDescription',
						'description',
						'isMaster',
						'minPrice' => function ($model) {
							return $model->minPrice;
						},
						'maxPrice' => function ($model) {
							return $model->maxPrice;
						},
                        'totalStock' => function ($model) {
                            /** @var Product $model */
                            return (int)$model->totalStock;
                        },
						'productCategory' => function ($model) {
							return ArrayHelper::toArray($model->productCategory, [
								ProductCategory::class => [
									'id',
									'name'
								]
							]);
						},
						'categoryBreadcrumb' => function($model){
							return $model->categoryBreadcrumb;
						},
						'productImages'      => function ($model) {
							return ArrayHelper::toArray($model->productImages, [
								ProductImages::class => [
									'id',
									'originalURL',
									'thumbnailURL',
									'isPrimary'
								]
							]);
						},
						'status'
					]
				],
				'apiCodeSuccess'    => ApiCode::DEFAULT_SUCCESS_CODE,
				'apiCodeFailed'     => ApiCode::DEFAULT_FAILED_CODE,
				'successMessage'    => \Yii::t('app', 'View Order Detail Success'),
			]
		];
	}

	public function actionDelete($id)
	{
		$product = Product::find()
			->where(['id' => $id])
			->one();
		if ($product) {
			$product->status = Product::STATUS_DELETED;
			$product->save();

			$response          = new Response();
			$response->name    = \Yii::t('app', 'Delete Product Success.');
			$response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
			$response->message = \Yii::t('app', 'Delete Product Success.');
			$response->status  = 200;
			$response->data    = [];
			return $response;
		}
		throw new NotFoundHttpException(\Yii::t('app', 'Product Not Found!'), 400);
	}

	public function verbs()
	{
		return [
			'list'   => ['get'],
			'create' => ['post'],
			'update' => ['post'],
			'delete' => ['post']
		];
	}
}