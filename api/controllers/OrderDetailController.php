<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\order\StoreOrderForm;
use api\forms\orderdetail\StoreOrderDetailForm;
use api\forms\orderdetail\UpdateOrderDetailForm;
use common\models\Order;
use common\models\OrderDetail;
use common\models\OrderStatus;
use common\models\ProductVariant;
use yii\helpers\ArrayHelper;

class OrderDetailController extends Controller {
    public function behaviors() {
        $behaviors                        = parent::behaviors();
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

    public function actions() {
        return [
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreOrderDetailForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Order Detail Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Order Detail Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateOrderDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Order Detail Success'),
                'messageFailed'  => \Yii::t('app', 'Update Order Detail Failed'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return OrderDetail::find()
                        ->joinWith(['order'])
                        ->addOrderBy([OrderDetail::tableName() . '.createdAt' => SORT_ASC]);
                },
                'filters'           => [
                    'orderId' => ['=', Order::tableName() . '.id']
                ],
                'toArrayProperties' => [
                    OrderDetail::class => [
                        'id',
                        'order'          => function ($model) {
                            /** @var OrderDetail $model */
                            return ArrayHelper::toArray($model->order, [
                                Order::class => [
                                    'id',
                                    'orderDate',
                                    'refInv',
                                    'orderStatus' => function ($model) {
                                        /** @var Order $model */
                                        return ArrayHelper::toArray($model->orderStatus, [
                                            OrderStatus::class => [
                                                'marketplaceId',
                                                'marketplaceStatusCode',
                                                'description',
                                            ]
                                        ]);
                                    },
                                ]
                            ]);
                        },
                        'productVariant' => function ($model) {
                            /** @var OrderDetail $model */
                            return ArrayHelper::toArray($model->productVariant, [
                                ProductVariant::class => [
                                    'sku',
                                    'marketplaceProductVariantId',
                                    'name'
                                ]
                            ]);
                        },
                        'quantity',
                        'weight',
                        'height',
                        'totalWeight',
                        'isFreeReturn',
                        'productPrice',
                        'insurancePrice',
                        'subTotalPrice'
                    ]
                ],
                'apiCodeSuccess'    => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'     => ApiCode::DEFAULT_FAILED_CODE,
                'successMessage'    => \Yii::t('app', 'Get Order Detail List Success'),
            ]
        ];
    }

    public function verbs() {
        return [
            'list'   => ['get'],
            'create' => ['post'],
            'update' => ['post'],
            'delete' => ['post']
        ];
    }
}