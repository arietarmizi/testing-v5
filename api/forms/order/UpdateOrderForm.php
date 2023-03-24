<?php


namespace api\forms\order;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Order;

class UpdateOrderForm extends BaseForm
{
    public $orderDate;
    public $refInv;
    public $customerId;
    public $courierId;
    public $warehouseId;
    public $promoId;
    public $discountId;
    public $orderStatus;

    public $_order;

    public function rules()
    {
        return [
            [['customerId', 'courierId', 'warehouseId'], 'required'],
            [['refInv', 'courierId', 'warehouseId', 'promoId'], 'string'],
            ['discountId', 'number'],
            ['orderDate', 'string'],
            ['orderStatus', 'in', 'range' => array_keys(Order::orderStatuses())],
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $order = Order::find()
            ->where(['id' => $findId])
            ->one();

        if (!$order) {
            throw new HttpException(400, \Yii::t('app', 'Order ID Not Found.'));
        }

        $order->orderDate   = $this->orderDate;
        $order->refInv      = $this->refInv;
        $order->customerId  = $this->customerId;
        $order->courierId   = $this->courierId;
        $order->warehouseId = $this->warehouseId;
        $order->promoId     = $this->promoId;
        $order->discountId  = $this->discountId;
        $order->orderStatus = $this->orderStatus;

        $success = true;

        if ($order->save())
            if ($order->hasErrors()) {
                $this->addError($order->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Order Failed.'));
            } else {
                $success &= $order->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}