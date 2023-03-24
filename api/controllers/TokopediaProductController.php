<?php

namespace api\controllers;

use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\product\CreateProductForm;
use api\forms\tokopedia\product\GetAllProductsForm;
use api\forms\tokopedia\product\GetInfoByIdForm;
use api\forms\tokopedia\product\GetInfoBySkuForm;
use api\forms\tokopedia\product\GetProductVariantForm;
use common\models\Product;

class TokopediaProductController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'get-all',
                'product-info-by-id',
                'product-info-by-sku',
                'get-variant',
                'create'
            ]
        ];
        return $behaviors;
    }

    public function Actions()
    {
        return [
            'get-all'             => [
                'class'          => FormAction::className(),
                'formClass'      => GetAllProductsForm::className(),
                'messageSuccess' => \Yii::t('app', 'Get All Products Success.'),
                'messageFailed'  => \Yii::t('app', 'Get All Products Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'product-info-by-id'  => [
                'class'          => FormAction::className(),
                'formClass'      => GetInfoByIdForm::className(),
                'messageSuccess' => \Yii::t('app', 'Get Info Products By ID Success.'),
                'messageFailed'  => \Yii::t('app', 'Get Info Products By ID Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'product-info-by-sku' => [
                'class'          => FormAction::className(),
                'formClass'      => GetInfoBySkuForm::className(),
                'messageSuccess' => \Yii::t('app', 'Get Info Products By SKU Success.'),
                'messageFailed'  => \Yii::t('app', 'Get Info Products By SKU Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'get-variant'         => [
                'class'          => FormAction::className(),
                'formClass'      => GetProductVariantForm::className(),
                'messageSuccess' => \Yii::t('app', 'Get Product Variant By Category ID Success.'),
                'messageFailed'  => \Yii::t('app', 'Get Product Variant By Category ID Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'create'              => [
                'class'          => FormAction::className(),
                'formClass'      => CreateProductForm::className(),
                'messageSuccess' => \Yii::t('app', 'Create Product Success.'),
                'messageFailed'  => \Yii::t('app', 'Create Product Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'                => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Product::find()
//                        ->where([Product::tableName() . '.status' => Product::STATUS_ACTIVE])
//                        ->where([Product::tableName() . '.categoryId' => Category::tableName() . '.id'])
//                        ->joinWith(['category'])
                        ->addOrderBy([Product::tableName() . '.name' => SORT_ASC]);
                },
                'toArrayProperties' => [
                    Product::class => [
                        'id',
                        'shopId',
                        'productSubCategoryId',
                        'name',
                        'condition',
                        'minOrder',
                        'productDescription',
                        'description',
                        'isMaster',
                        'status'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get product list success'),
            ],
        ];
    }

    public function verbs()
    {
        return [
            'get-all'             => ['post'],
            'product-info-by-id'  => ['post'],
            'product-info-by-sku' => ['post'],
            'get-variant'         => ['post'],
            'create'              => ['post']
        ];
    }
}
