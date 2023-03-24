<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\productdiscount\StoreProductDiscountForm;
use api\forms\productdiscount\UpdateProductDiscountForm;
use common\models\ProductDiscount;
use common\models\ProductVariant;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ProductDiscountController extends Controller
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
                'formClass'      => StoreProductDiscountForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Discount Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Discount Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateProductDiscountForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Product Discount Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Product Discount Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return ProductDiscount::find()
                        ->where([ProductDiscount::tableName() . '.status' => ProductDiscount::STATUS_ACTIVE])
                        ->addOrderBy([ProductDiscount::tableName() . '.id' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    ProductDiscount::class => [
                        'productVariant' => function ($model) {
                            return ArrayHelper::toArray($model->productVariant, [
                                ProductVariant::class => [
                               'id',
                               'name'
                                ]
                            ]);
                        },
                        'discountPrice',
                        'discountPercentage',
                        'startTime',
                        'endTime',
                        'initialQuota',
                        'remainingQuota',
                        'maxOrder',
                        'slashPriceStatusId',
                        'useWarehouse',
                        'status'
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Product Discount List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = ProductDiscount::find()
            ->where(['id' => $id])
            ->one();

        if ($query) {
            $query->status = ProductDiscount::STATUS_DELETED;
            $query->save();
            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Discount Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Discount Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Discount Id Not found!.'), 400);
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