<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\productbundle\StoreProductBundleForm;
use api\forms\productbundle\UpdateProductBundleForm;
use common\models\ProductBundle;
use yii\web\NotFoundHttpException;

class ProductBundleController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'store',
                'update'
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'store'  => [
                'class'          => FormAction::className(),
                'formClass'      => StoreProductBundleForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Bundle Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Bundle Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateProductBundleForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Product Bundle Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Product Bundle Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return ProductBundle::find()
                        ->where([ProductBundle::tableName() . '.status' => ProductBundle::STATUS_ACTIVE])
                        ->addOrderBy([ProductBundle::tableName() . '.name' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    ProductBundle::class => [
                        'name',
                        'price',
                        'description',
                        'status'
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Product Bundle List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $query = ProductBundle::find()
            ->where(['id' => $id])
            ->andWhere(['status' => ProductBundle::STATUS_ACTIVE])
            ->one();
        if ($query) {
            $query->status = ProductBundle::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Bundle Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Bundle Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Bundle ID Not Found!'), 400);
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