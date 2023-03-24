<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\productbundledetail\StoreProductBundleDetailForm;
use api\forms\productbundledetail\UpdateProductBundleDetailForm;
use common\models\ProductBundle;
use common\models\ProductBundleDetail;
use common\models\ProductVariant;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ProductBundleDetailController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
                'update'
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreProductBundleDetailForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Bundle Detail Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Bundle Detail Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateProductBundleDetailForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Product Bundle Detail Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Product Bundle Detail Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return ProductBundleDetail::find()
                        ->andWhere([ProductBundleDetail::tableName() . '.status' => ProductBundleDetail::STATUS_ACTIVE])
                        ->addOrderBy([ProductBundleDetail::tableName() . '.createdAt' => SORT_DESC]);
                },
                'toArrayProperties' => [
                    ProductBundleDetail::class => [
                        'productBundle'  => function ($model) {
                            return ArrayHelper::toArray($model->productBundle, [
                                ProductBundle::class => [
                                    'id',
                                    'name',
                                    'price',
                                    'description'
                                ]
                            ]);
                        },
                        'productVariant' => function ($model) {
                            return ArrayHelper::toArray($model->productVariant, [
                                ProductVariant::class => [
                                    'id',
                                    'name',
                                    'description',
                                    'productDescription'
                                ]
                            ]);
                        },
                        'quantity',
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Product Bundle Detail List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = ProductBundleDetail::find()
            ->where(['id' => $id])
            ->andWhere(['status' => ProductBundleDetail::STATUS_ACTIVE])
            ->one();
        if ($query) {
            $query->status = ProductBundleDetail::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Bundle Detail success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Bundle Detail success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Bundle Detail ID Not Found!'), 400);
    }

    public function verbs()
    {
        return [
            'store'  => ['post'],
            'update' => ['post'],
            'delete' => ['post'],
            'list'   => ['get'],
        ];
    }
}