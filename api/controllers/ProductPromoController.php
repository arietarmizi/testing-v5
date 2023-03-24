<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\productpromo\StoreProductPromoForm;
use api\forms\productpromo\UpdateProductPromoForm;
use common\models\ProductPromo;
use yii\web\NotFoundHttpException;

class ProductPromoController extends Controller
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
                'formClass'      => StoreProductPromoForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Promo Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Promo Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::className(),
                'formClass'      => UpdateProductPromoForm::className(),
                'messageSuccess' => \Yii::t('app', 'Update Product Promo Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Product Promo Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return ProductPromo::find()
//                        ->where([ProductBundleDetail::tableName() . '.status' => ProductBundleDetail::STATUS_ACTIVE])
                        ->addOrderBy([ProductPromo::tableName() . '.id' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    ProductPromo::class => [
                        'productVariantId',
                        'minQuantity',
                        'maxQuantity',
                        'defaultPrice',
                    ],
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Product Promo List Success'),
            ]
        ];
    }


    public function actionDelete($id)
    {
        $query = ProductPromo::find()
            ->where(['id' => $id])
            ->one();

        if ($query) {
            $query->status = ProductPromo::STATUS_DELETED;
            $query->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Product Promo Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Product Promo Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Product Promo ID Not Found!'), 400);
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