<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\stockmanagement\StoreStockManagementForm;
use api\forms\stockmanagement\UpdateStockManagementForm;
use common\models\ProductVariant;
use common\models\StockManagement;
use common\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class StockManagementController extends Controller
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
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreStockManagementForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Stock Management Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Stock management Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateStockManagementForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Stock Management Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Stock management Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return StockManagement::find()
                        ->where([StockManagement::tableName() . '.status' => StockManagement::STATUS_ACTIVE])
                        ->addOrderBy([StockManagement::tableName() . '.id' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    StockManagement::class => [
                        'productVariant' => function ($model) {
                            return ArrayHelper::toArray($model->productVariant, [
                                ProductVariant::class => [
                                    'id',
                                    'name',
                                    'description',
                                    'productDescription',
                                    'defaultPrice'
                                ]
                            ]);
                        },
                        'promotionStock',
                        'orderedStock',
                        'availableStock',
                        'onHandStock',
                        'stockType',
                        'warehouse'      => function ($model) {
                            return ArrayHelper::toArray($model->warehouse, [
                                Warehouse::class => [
                                    'id',
                                    'name',
                                    'description',
                                    'address'
                                ]
                            ]);
                        },
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Stock Management List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = StockManagement::find()
            ->where(['id' => $id])
            ->one();

        if ($query) {
            $query->status = StockManagement::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Stock Management Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Stock Management Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Stock Management ID Not Found!.'), 400);
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