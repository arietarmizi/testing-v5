<?php


namespace api\controllers;


use api\actions\ListAction;
use api\actions\ViewAction;
use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\order\StoreOrderForm;
use api\forms\order\tokopedia\DownloadOrderForm;
use api\forms\order\UpdateOrderForm;
use common\models\CourierInformation;
use common\models\Customer;
use common\models\Order;
use common\models\OrderDetail;
use common\models\OrderStatus;
use common\models\ProductDiscount;
use common\models\ProductPromo;
use common\models\ProductVariant;
use common\models\Shipment;
use common\models\ShipmentService;
use common\models\Warehouse;
use yii\helpers\ArrayHelper;

class OrderController extends Controller {
    public function behaviors() {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
                'update',
                'download'
            ]
        ];
        return $behaviors;
    }

    public function actions() {
        return [
            'store'    => [
                'class'          => FormAction::className(),
                'formClass'      => StoreOrderForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Order Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Order Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update'   => [
                'class'          => FormAction::class,
                'formClass'      => UpdateOrderForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Order Success'),
                'messageFailed'  => \Yii::t('app', 'Update Order Failed'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'list'     => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Order::find()
                        ->joinWith(['orderStatus'])
                        ->addOrderBy([Order::tableName() . '.createdAt' => SORT_ASC]);
                },
                'filters'           => [
                    'statusCode' => ['=', OrderStatus::tableName() . '.marketplaceStatusCode']
                ],
                'toArrayProperties' => [
                    Order::class => [
                        'id',
                        'orderDate',
                        'refInv',
                        'orderStatus'     => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->orderStatus, [
                                OrderStatus::class => [
                                    'marketplaceId',
                                    'marketplaceStatusCode',
                                    'description',
                                ]
                            ]);
                        },
                        'customer'        => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->customer, [
                                Customer::class => [
                                    'marketplaceCustomerId',
                                    'customerName',
                                    'email',
                                    'phoneNumber',
                                    'address'
                                ]
                            ]);
                        },
                        'shipment'        => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->shipment, [
                                Shipment::class => [
                                    'marketplaceShipmentId',
                                    'name',
                                ]
                            ]);
                        },
                        'shipmentService' => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->shipmentService, [
                                ShipmentService::class => [
                                    'marketplaceShipmentServiceId',
                                    'name'
                                ]
                            ]);
                        },
                        'warehouse'       => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->warehouse, [
                                Warehouse::class => [
                                    'name',
                                    'address'
                                ]
                            ]);
                        },
                        'productPromo'    => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->productPromo, [
                                ProductPromo::class => [
                                    'productVariantId',
                                    'minQuantity',
                                    'maxQuantity',
                                    'defaultPrice'
                                ]
                            ]);
                        },
                        'productDiscount' => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->productDiscount, [
                                ProductDiscount::class => [
                                    'discountPrice',
                                    'discountPercentage',
                                ]
                            ]);
                        },
						'total'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Order List Success'),
            ],
            'download' => [
                'class'          => FormAction::class,
                'formClass'      => DownloadOrderForm::class,
                'messageSuccess' => \Yii::t('app', 'Download Order Success.'),
                'messageFailed'  => \Yii::t('app', 'Download Order Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'detail'   => [
                'class'             => ViewAction::class,
                'query'             => function () {
                    return Order::find();
                },
                'toArrayProperties' => [
                    Order::class => [
                        'id',
                        'orderDate',
                        'refInv',
                        'orderStatus'     => function ($model) {
                            /** @var Order $model */
                            return $model->orderStatus->description;
                        },
                        'customer'        => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->customer, [
                                Customer::class => [
                                    'marketplaceCustomerId',
                                    'customerName',
                                    'email',
                                    'phoneNumber',
                                    'address'
                                ]
                            ]);
                        },
                        'shipment'        => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->shipment, [
                                Shipment::class => [
                                    'marketplaceShipmentId',
                                    'name',
                                    'isAvailable'
                                ]
                            ]);
                        },
                        'shipmentService' => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->shipmentService, [
                                ShipmentService::class => [
                                    'marketplaceShipmentServiceId',
                                    'name',
                                    'isAvailable'
                                ]
                            ]);
                        },
                        'warehouse'       => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->warehouse, [
                                Warehouse::class => [
                                    'name',
                                    'address'
                                ]
                            ]);
                        },
                        'productPromo'    => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->productPromo, [
                                ProductPromo::class => [
                                    'productVariantId',
                                    'minQuantity',
                                    'maxQuantity',
                                    'defaultPrice'
                                ]
                            ]);
                        },
                        'productDiscount' => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->productDiscount, [
                                ProductDiscount::class => [
                                    'discountPrice',
                                    'discountPercentage',
                                ]
                            ]);
                        },
                        'orderDetails'    => function ($model) {
                            /** @var Order $model */
                            return ArrayHelper::toArray($model->orderDetails, [
                                OrderDetail::class => [
                                    'id',
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
                            ]);
                        },
                        'createdAt',
                        'updatedAt'
                    ]
                ],
                'apiCodeSuccess'    => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'     => ApiCode::DEFAULT_FAILED_CODE,
                'successMessage'    => \Yii::t('app', 'View Order Detail Success'),
            ]
        ];
    }

    public function verbs() {
        return [
            'store'    => ['post'],
            'update'   => ['post'],
            'delete'   => ['post'],
            'list'     => ['get'],
            'detail'   => ['get'],
            'download' => ['post']
        ];
    }
}