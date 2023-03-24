<?php

namespace api\forms\orderdetail;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\OrderDetail;
use common\models\ProductVariant;

class StoreOrderDetailForm extends BaseForm
{
    public $orderId;
    public $productVariantId;
    public $quantity;
    public $weight;
    public $height;
    public $totalWeight;
    public $isFreeReturn;
    public $productPrice;
    public $insurancePrice;
    public $subTotalPrice;
    public $notes;

    protected $_orderDetail;

    public function rules()
    {
        return [
            [['orderId', 'quantity'], 'required'],
            [['orderId', 'productVariantId', 'notes'], 'string'],
            [['quantity', 'weight', 'height', 'totalWeight', 'productPrice', 'insurancePrice', 'subTotalPrice'], 'double'],
            ['isFreeReturn', 'boolean'],
            ['productVariantId', 'validateProductVariant'],
        ];
    }

    public function validateProductVariant()
    {
        $productVariant = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->one();

        if (!$productVariant) {
            throw new HttpException(400, \Yii::t('app', 'Product Variant Not Found.'));
        }
    }

    public function submit()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $query                   = new OrderDetail();
        $query->orderId          = $this->orderId;
        $query->productVariantId = $this->productVariantId;
        $query->quantity         = $this->quantity;
        $query->weight           = $this->weight;
        $query->height           = $this->height;
        $query->totalWeight      = $this->totalWeight;
        $query->isFreeReturn     = $this->isFreeReturn;
        $query->productPrice     = $this->productPrice;
        $query->insurancePrice   = $this->insurancePrice;
        $query->subTotalPrice    = $this->subTotalPrice;
        $query->notes            = $this->notes;

        $query->save(false);
        if ($query->save()) {
            $query->refresh();
            $this->_orderDetail = $query;
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
        $response = $this->_orderDetail->toArray();

        unset($response['createdAt']);
        unset($response['updatedAt']);

        return ['orderDetail' => $response];
    }
}