<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\filters\ContentTypeFilter;
use common\models\Marketplace;
use common\models\OrderStatus;
use yii\helpers\ArrayHelper;

class OrderStatusController extends Controller {
    public function behaviors() {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => []
        ];
        return $behaviors;
    }

    public function actions() {
        return [
            'list' => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return OrderStatus::find()->joinWith(['marketplace']);
                },
                'filters'           => [
                    'marketplaceId'         => ['=', OrderStatus::tableName() . '.marketplaceId'],
                    'marketplaceCode'       => ['=', Marketplace::tableName() . '.code'],
                    'marketplaceStatusCode' => ['=', OrderStatus::tableName() . '.marketplaceStatusCode']
                ],
                'toArrayProperties' => [
                    OrderStatus::class => [
                        'marketplace' => function ($model) {
                            /** @var OrderStatus $model */
                            return ArrayHelper::toArray($model->marketplace, [
                                Marketplace::class => [
                                    'id',
                                    'code',
                                    'marketplaceName'
                                ]
                            ]);
                        },
                        'marketplaceStatusCode',
                        'description'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Order Status List Success'),
            ]
        ];
    }

    public function verbs() {
        return [
            'list' => ['get']
        ];
    }
}