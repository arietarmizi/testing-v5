<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\shipment\tokopedia\DownloadShipmentForm;
use api\forms\shipment\tokopedia\UpdateShipmentForm;
use common\models\Shipment;
use common\models\ShipmentService;
use common\models\Shop;
use yii\helpers\ArrayHelper;

class ShipmentController extends Controller {
    public function behaviors() {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'download',
                'update'
            ]
        ];
        return $behaviors;
    }

    public function actions() {
        return [
            'list'     => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Shipment::find()->joinWith(['shipmentServices', 'shop']);
                },
                'filters'           => [
                    'marketplaceShopId'          => ['=', Shop::tableName() . '.marketplaceShopId'],
                    'marketplaceShipmentId'      => ['=', Shipment::tableName() . '.marketplaceShipmentId'],
                    'shipmentName'               => ['like', Shipment::tableName() . '.name'],
                    'shipmentServiceName'        => ['like', ShipmentService::tableName() . '.name'],
                    'isAvailableShipment'        => ['=', Shipment::tableName() . '.isAvailable'],
                    'isAvailableShipmentService' => ['=', ShipmentService::tableName() . '.isAvailable']
                ],
                'toArrayProperties' => [
                    Shipment::class => [
                        'id',
                        'shop'             => function ($model) {
                            /** @var Shipment $model */
                            return ArrayHelper::toArray($model->shop, [
                                Shop::class => [
                                    'id',
                                    'marketplaceShopId',
                                    'marketplaceId',
                                    'fsId',
                                    'userId',
                                    'shopName',
                                    'isOpen' => function ($model) {
                                        /** @var Shop $model */
                                        return ArrayHelper::getValue(Shop::openStatuses(), $model->isOpen);
                                    }
                                ]
                            ]);
                        },
                        'marketplaceShipmentId',
                        'name',
                        'isAvailable'      => function ($model) {
                            /** @var Shipment $model */
                            return ArrayHelper::getValue(Shipment::available(), $model->isAvailable);
                        },
                        'shipmentServices' => function ($model) {
                            /** @var Shipment $model */
                            return ArrayHelper::toArray($model->shipmentServices, [
                                ShipmentService::class => [
                                    'id',
                                    'shipmentId',
                                    'marketplaceShipmentServiceId',
                                    'name',
                                    'isAvailable' => function ($model) {
                                        /** @var ShipmentService $model */
                                        return ArrayHelper::getValue(ShipmentService::available(), $model->isAvailable);
                                    }
                                ]
                            ]);
                        }
                    ]
                ],
                'apiCodeSuccess'    => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'     => ApiCode::DEFAULT_FAILED_CODE,
                'successMessage'    => \Yii::t('app', 'Get Shipment List Success'),
            ],
            'download' => [
                'class'          => FormAction::class,
                'formClass'      => DownloadShipmentForm::class,
                'messageSuccess' => \Yii::t('app', 'Download Shipment Success.'),
                'messageFailed'  => \Yii::t('app', 'Download Shipment Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update'   => [
                'class'          => FormAction::class,
                'formClass'      => UpdateShipmentForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Shipment success'),
                'messageFailed'  => \Yii::t('app', 'Update Shipment failed'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ]
        ];
    }

    public function verbs() {
        return [
            'list'     => ['get'],
            'download' => ['post'],
            'update'   => ['post']
        ];
    }
}