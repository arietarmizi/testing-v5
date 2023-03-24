<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\warehouse\StoreWarehouseForm;
use api\forms\warehouse\UpdateWarehouseForm;
use common\models\Warehouse;
use yii\web\NotFoundHttpException;

class WarehouseController extends Controller
{
    public function behaviors()
    {
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

    public function actions()
    {
        return [
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreWarehouseForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create  Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Warehouse Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateWarehouseForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Warehouse Success'),
                'messageFailed'  => \Yii::t('app', 'Update Warehouse Failed'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Warehouse::find()
                        ->where([Warehouse::tableName() . '.status' => Warehouse::STATUS_ACTIVE])
                        ->addOrderBy([Warehouse::tableName() . '.createdAt' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    Warehouse::class => [
                        'shopId',
                        'name',
                        'description',
                        'subDistrictId',
                        'address',
                        'email',
                        'phoneNumber',
                        'whType',
                        'isDefault',
                        'latLon',
                        'latitude',
                        'longitude',
                        'branchShopSubscription',
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Warehouse List Success.'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $warehouse = Warehouse::find()
            ->where(['id' => $id])
            ->one();
        if ($warehouse){
            $warehouse->status = Warehouse::STATUS_DELETED;
            $warehouse->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Warehouse Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Warehouse Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Warehouse ID Not Found!'), 400);
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