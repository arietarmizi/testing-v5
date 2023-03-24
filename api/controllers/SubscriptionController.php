<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\subscription\StoreSubscriptionForm;
use common\models\Subscription;
use common\models\User;
use yii\helpers\ArrayHelper;

class SubscriptionController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'store' => [
                'class'          => FormAction::class,
                'formClass'      => StoreSubscriptionForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Subscription Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Subscription Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => StoreSubscriptionForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Subscription Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Subscription Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'  => [
                'class' => ListAction::class,
                'query' => function () {
                    return Subscription::find()
                        ->joinWith(['subscriptionType', 'user']);
                },
                'toArrayProperties' => [
                    Subscription::class => [
                        'id',
                        'subscriptionType' => function ($model) {
                            /** @var Subscription $model */
                            return [
                                'id'                => $model->subscriptionType->id,
                                'name'              => $model->subscriptionType->name,
                                'duration'          => $model->subscriptionType->duration,
                                'durationType'      => $model->subscriptionType->durationType,
                                'isSupportMultiple' => $model->subscriptionType->isSupportMultiple,
                                'transactionQuota'  => $model->subscriptionType->transactionQuota,
                                'price'             => $model->subscriptionType->price,
                                'description'       => $model->subscriptionType->description,
                                'priority'          => $model->subscriptionType->priority,
                            ];
                        },
                        'user'             => function ($model) {
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
                        'registerAt',
                        'expiredAt',
                        'usedQuota',
                        'remainingQuota',
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get subscription list success'),
            ]
        ];
    }

    public function verbs()
    {
        return [
            'store' => ['post'],
        ];
    }
}