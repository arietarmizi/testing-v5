<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\masterstatus\StoreMasterStatusForm;
use api\forms\masterstatus\UpdateMasterStatusForm;
use common\models\Marketplace;
use common\models\MasterStatus;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MasterStatusController extends Controller
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
                'formClass'      => StoreMasterStatusForm::className(),
                'messageSuccess' => \Yii::t('app', 'Store Master Status Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Master Status Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateMasterStatusForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Master Status Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Master Status Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE
            ],
            'list'   => [
                'class' => ListAction::class,
                'query' => function () {
                    return MasterStatus::find()
                        ->joinWith(['marketplace']);
                },
                'toArrayProperties' => [
                    MasterStatus::class => [
                        'id',
                        'marketplace' => function ($model) {
                            /** @var MasterStatus $model */
                            return ['id' => $model->marketplaceId, 'name' => $model->marketplace->marketplaceName];
                        },
                        'statusCode',
                        'desc'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get master status list success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = MasterStatus::find()
            ->where([MasterStatus::tableName() . '.id' => $id])
            ->andWhere([MasterStatus::tableName() . '.status' => MasterStatus::STATUS_ACTIVE])
            ->one();
        if ($query) {
            $query->status = MasterStatus::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Master Status Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Master Status Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Master Status Not Found!'), 400);
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