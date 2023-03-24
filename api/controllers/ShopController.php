<?php

namespace api\controllers;

use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\shop\StoreShopForm;
use api\forms\shop\UpdateShopForm;
use api\forms\tokopedia\ShopShowCaseForm;
use common\models\Shop;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ShopController extends Controller {
    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
                'update'
            ],
        ];
        return $behaviors;
    }

    public function actions() {
        return [
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreShopForm::className(),
                'messageSuccess' => \Yii::t('app', 'Store Shop Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Shop Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateShopForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Shop Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Shop Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Shop::find()
                        ->joinWith(['marketplace', 'user']);
                },
                'filters'           => [
                    'marketplaceShopId' => ['=', Shop::tableName() . '.marketplaceShopId'],
                    'marketplaceId'     => ['=', Shop::tableName() . '.marketplaceId'],
                    'userId'            => ['=', User::tableName() . '.id']
                ],
                'toArrayProperties' => [
                    Shop::class => [
                        'id',
                        'fsId',
                        'marketplaceShopId',
                        'marketplace' => function ($model) {
                            /** @var Shop $model */
                            return [
                                'id'   => $model->marketplace->id,
                                'code' => $model->marketplace->code,
                                'name' => $model->marketplace->marketplaceName,
                            ];
                        },
                        'user'        => function ($model) {
                            /** @var User $model */
                            return [
                                'id'          => $model->user->id,
                                'name'        => $model->user->name,
                                'phoneNumber' => $model->user->phoneNumber,
                                'email'       => $model->user->email,
                                'birthDate'   => $model->user->birthDate,
                                'address'     => $model->user->address,
                                'status'      => $model->user->status,
                            ];
                        },
                        'shopName',
                        'shopLogo',
                        'shopUrl',
                        'description',
                        'domain',
                        'isOpen',
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Shop List Success'),
            ]
        ];
    }

    public function actionDelete($id) {
        $shop = Shop::find()
            ->where(['id' => $id])
            ->one();
        if ($shop) {
            $shop->status = Shop::STATUS_DELETED;
            $shop->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Shop Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Shop Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Shop ID Not Found!'), 400);
    }

    protected function verbs() {
        return [
            'store'  => ['post'],
            'update' => ['post'],
            'delete' => ['post'],
            'list'   => ['get']
        ];
    }
}
