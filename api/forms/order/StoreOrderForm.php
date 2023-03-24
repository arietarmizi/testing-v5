<?php


namespace api\forms\order;


use api\components\BaseForm;
use common\models\Order;

class StoreOrderForm extends BaseForm
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
            ['orderStatus', 'in', 'range' => array_keys(Order::orderStatuses())]
        ];
    }

    public function submit()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $order              = new Order();
        $order->orderDate   = $this->orderDate;
        $order->refInv      = $this->refInv;
        $order->customerId  = $this->customerId;
        $order->courierId   = $this->courierId;
        $order->warehouseId = $this->warehouseId;
        $order->promoId     = $this->promoId;
        $order->discountId  = $this->discountId;
        $order->orderStatus = $this->orderStatus;

        $order->save();
        if ($order->save()) {
            $order->refresh();
            $this->_order = $order;
            $transaction->commit();
            return true;
        } else {
            $this->addErrors($this->getErrors());
            $transaction->rollBack();
            return false;
        }
    }

    public function response()
    {
        $response = $this->_order->toArray();

        unset($response['createdAt']);
        unset($response['updatedAt']);

        return ['product' => $response];
    }
}