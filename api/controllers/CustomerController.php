<?php


namespace api\controllers;


use api\actions\ListAction;
use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\customer\StoreCustomerForm;
use api\forms\customer\UpdateCustomerForm;
use common\models\Customer;
use yii\web\NotFoundHttpException;

class CustomerController extends Controller
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
                'formClass'      => StoreCustomerForm::class,
                'messageSuccess' => \Yii::t('app', 'Store Customer Success.'),
                'messageFailed'  => \Yii::t('app', 'Store Customer Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'update' => [
                'class'          => FormAction::class,
                'formClass'      => UpdateCustomerForm::class,
                'messageSuccess' => \Yii::t('app', 'Update Customer Success.'),
                'messageFailed'  => \Yii::t('app', 'Update Customer Failed.'),
                'apiCodeSuccess' => ApiCode::DEFAULT_SUCCESS_CODE,
                'apiCodeFailed'  => ApiCode::DEFAULT_FAILED_CODE,
            ],
            'list'   => [
                'class'             => ListAction::class,
                'query'             => function () {
                    return Customer::find()
                        ->where([Customer::tableName() . '.status' => Customer::STATUS_ACTIVE])
                        ->addOrderBy([Customer::tableName() . '.createdAt' => SORT_DESC]);
                },
                'toArrayProperties' => [
                    Customer::class => [
                        'customerName',
                        'email',
                        'phoneNumber',
                        'address'
                    ]
                ],
                'apiCodeSuccess'    => 0,
                'apiCodeFailed'     => 400,
                'successMessage'    => \Yii::t('app', 'Get Customer List Success'),
            ]
        ];
    }

    public function actionDelete($id)
    {
        $customer = Customer::find()
            ->where(['id' => $id])
            ->one();
        if ($customer) {
            $customer->status = Customer::STATUS_DELETED;
            $customer->save();

            $response          = new Response();
            $response->name    = \Yii::t('app', 'Delete Customer Success.');
            $response->code    = ApiCode::DEFAULT_SUCCESS_CODE;
            $response->message = \Yii::t('app', 'Delete Customer Success.');
            $response->status  = 200;
            $response->data    = [];
            return $response;
        }
        throw new NotFoundHttpException(\Yii::t('app', 'Customer ID Not Found!'), 400);
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