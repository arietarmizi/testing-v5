<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\marketplace\StoreMarketplaceForm;
use api\forms\marketplace\UpdateMarketplaceForm;
use common\models\Marketplace;
use yii\web\NotFoundHttpException;

class MarketplaceController extends Controller
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
                'formClass'      => StoreMarketplaceForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Marketplace Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Marketplace Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateMarketplaceForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Marketplace Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Marketplace Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Marketplace::find()
                        ->where([Marketplace::tableName() . '.status' => Marketplace::STATUS_ACTIVE])
                        ->addOrderBy([Marketplace::tableName() . '.marketplaceName' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    Marketplace::class => [
                        'marketplaceName',
                        'description',
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Marketplace List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $marketplace = Marketplace::find()
            ->where(['id' => $id])
            ->one();
        if ($marketplace) {
            $marketplace->status = Marketplace::STATUS_DELETED;
            $marketplace->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Not Found!'), 400);
    }


    public function verbs()
    {
        return [
            'store'  => ['post'],
            'update' => ['post']
        ];
    }

}