<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\subscriptiontype\StoreSubscriptionTypeForm;
use api\forms\subscriptiontype\UpdateSubscriptionTypeForm;
use common\models\SubscriptionType;
use yii\web\NotFoundHttpException;

class SubscriptionTypeController extends Controller
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
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'store'  => [
                'class'          => FormAction::class,
                'formClass'      => StoreSubscriptionTypeForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Subscription Type Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Subscription Type Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateSubscriptionTypeForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Subscription Type Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Subscription Type Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return SubscriptionType::find()
//                        ->where([SubscriptionType::tableName() . '.status' => SubscriptionType::STATUS_ACTIVE])
                        ->addOrderBy([SubscriptionType::tableName() . '.name' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    SubscriptionType::class => [
                        'name',
                        'duration',
                        'durationType',
                        'isSupportMultiple',
                        'transactionQuota',
                        'price',
                        'description',
                        'priority',
                        'status'
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Subscription Type List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = SubscriptionType::find()
            ->where([SubscriptionType::tableName() . '.id' => $id])
            ->andWhere([SubscriptionType::tableName() . '.status' => SubscriptionType::STATUS_ACTIVE])
            ->one();
        if ($query) {
            $query->status = SubscriptionType::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Subscription Type Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Subscription Type Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Subscription Type Not Found!'), 400);
    }

    public function verbs()
    {
        return [
            'store'  => ['post'],
            'update' => ['post'],
            'list'   => ['get'],
            'delete' => ['post']
        ];
    }
}