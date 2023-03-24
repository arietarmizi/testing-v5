<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\courier\StoreCourierForm;
use api\forms\courier\UpdateCourierForm;
use common\models\CourierInformation;
use yii\web\NotFoundHttpException;

class CourierInformationController extends Controller
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
                'class'          => FormAction::class,
                'formClass'      => StoreCourierForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Courier Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Courier Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateCourierForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Courier Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Courier Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return CourierInformation::find()
                        ->where([CourierInformation::tableName() . '.status' => CourierInformation::STATUS_ACTIVE])
                        ->addOrderBy([CourierInformation::tableName() . '.createdAt' => SORT_DESC]);
                },
                'toArrayProperties' => [
                    CourierInformation::class => [
                        'marketplaceCourierId',
                        'courierName',
                        'phoneNumber',
                        'notes'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Courier List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $courier = CourierInformation::find()
            ->where(['id' => $id])
            ->one();
        if ($courier) {
            $courier->status = CourierInformation::STATUS_DELETED;
            $courier->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Courier Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Courier Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Courier ID Not Found!'), 400);
    }

    public function verbs()
    {
        return [
            'store'  => ['post'],
            'update' => ['post']
        ];
    }
}