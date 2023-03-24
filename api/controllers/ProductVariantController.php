<?php


namespace api\controllers;


use api\actions\ListAction;
use api\actions\ViewAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\productvariant\ProductVariantAddImagesForm;
use api\forms\productvariant\StoreProductVariantForm;
use api\forms\productvariant\UpdateProductVariantForm;
use common\models\Product;
use common\models\ProductImages;
use common\models\ProductVariant;
use common\models\ProductVariantImages;
use common\models\StockManagement;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ProductVariantController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
                'update',
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'store'     => [
                'class'          => FormAction::className(),
                'formClass'      => StoreProductVariantForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Variant Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Variant Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update'    => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateProductVariantForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Product Variant Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Product Variant Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'add-image' => [
                'class'          => FormAction::class,
                'formClass'      => ProductVariantAddImagesForm::class,
                'messageSuccess' => \Yii::t('app', 'Add image product variant success'),
                'messageFailed'  => \Yii::t('app', 'Add image product variant failed'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'list' => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return ProductVariant::find()
                        ->joinWith(['product']);
                },
                'filters' => [
                  'productId', ['=', ProductVariant::tableName() . '.productId']
                ],
                'toArrayProperties' => [
                    ProductVariant::class => [
                        'id',
                        'product' => function ($model) {
                            /** @var ProductVariant $model */
                            return ArrayHelper::toArray($model->product, [
                            	Product::class => [
									'id',
									'code',
									'name',
									'condition',
									'productDescription',
									'description',
									'isMaster',
								]
							]);
                        },
                        'sku',
                        'name',
                        'isShelfLife',
                        'duration',
                        'inboundLimit',
                        'outboundLimit',
                        'minOrder',
                        'productDescription',
                        'description',
                        'defaultPrice',
                        'length',
                        'width',
                        'height',
                        'weight',
                        'barcode',
                        'isPreOrder',
                        'minPreOrderDay',
                        'discount',
                        'isWholesale',
                        'isFreeReturn',
                        'isMustInsurance',
						'stock' => function ($model) {
							/** @var ProductVariant $model */
							return ArrayHelper::toArray($model->stock, [
								StockManagement::class => [
									'id',
									'availableStock',
								]
							]);
						},
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get product variant list success'),
            ],
			'detail' => [
				'class'             => ViewAction::class,
				'query'             => function () {
					return ProductVariant::find()
						->joinWith(['product']);
				},
				'toArrayProperties' => [
					ProductVariant::class => [
						'id',
						'product' => function ($model) {
							/** @var ProductVariant $model */
							return ArrayHelper::toArray($model->product, [
								Product::class => [
									'id',
									'code',
									'name',
									'condition',
									'productDescription',
									'description',
									'isMaster',
								]
							]);
						},
						'sku',
						'name',
						'isShelfLife',
						'duration',
						'inboundLimit',
						'outboundLimit',
						'minOrder',
						'productDescription',
						'description',
						'defaultPrice',
						'length',
						'width',
						'height',
						'weight',
						'barcode',
						'isPreOrder',
						'minPreOrderDay',
						'discount',
						'isWholesale',
						'isFreeReturn',
						'isMustInsurance',
						'stock' => function ($model) {
							/** @var ProductVariant $model */
							return ArrayHelper::toArray($model->stock, [
								StockManagement::class => [
									'id',
									'availableStock',
								]
							]);
						},
						'images' => function ($model) {
							/** @var ProductVariant $model */
							return ArrayHelper::toArray($model->images, [
								ProductVariantImages::class => [
									'id',
									'isPrimary',
									'originalURL',
									'thumbnailURL',
								]
							]);
						},
						'status'
					]
				],
				'apiCodeSuccess'    => 0,
				'apiCodeFailed'     => 400,
				'successMessage'    => \Yii::t('app', 'Get product variant list success'),
			]
        ];
    }

    public function actionDelete($id)
    {
        $query = ProductVariant::find()
            ->where(['id' => $id])
            ->one();
        if ($query) {
            $query->status = ProductVariant::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Variant Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Variant Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Variant Not Found!'), 400);
    }

    public function verbs()
    {
        return [
            'store'  => ['post'],
            'update' => ['post'],
            'delete' => ['post'],
            'list'   => ['get']
        ];
    }
}