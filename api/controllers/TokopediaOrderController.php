<?php


namespace api\controllers;


use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\tokopedia\order\GetSingleOrderForm;

class TokopediaOrderController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'get-single-order'
            ]
        ];
        return $behaviors;
    }

    public function Actions()
    {
        return [
            'get-single-order' => [
                'class'          => FormAction::className(),
                'formClass'      => GetSingleOrderForm::className(),
                'messageSuccess' => \Yii::t('app', 'Get Single Order Success.'),
                'messageFailed'  => \Yii::t('app', 'Get Single Order Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE
            ],
        ];
    }

    public function verbs()
    {
        return [
            'get-single-order' => ['post'],
        ];
    }
}