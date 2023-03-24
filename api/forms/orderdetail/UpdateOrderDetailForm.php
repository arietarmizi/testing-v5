<?php


namespace api\forms\orderdetail;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Order;
use common\models\OrderDetail;
use common\models\ProductVariant;

class UpdateOrderDetailForm extends BaseForm
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
            [['quantity', 'weight', 'height', 'totalWeight', 'productPrice', 'insurancePrice', 'subTotalPrice'], 'number'],
            ['isFreeReturn', 'boolean'],
            ['productVariantId', 'validateProductVariant'],
            ['orderId', 'validateOrderId']
        ];
    }

    public function validateProductVariant()
    {
        $productVariant = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->one();

        if (!$productVariant) {
            throw new HttpException(400, \Yii::t('app', 'Product Variant ID Not Found.'));
        }
    }

    public function validateOrderId()
    {
        $order = Order::find()
            ->where(['id' => $this->orderId])
            ->one();
        if (!$order) {
            throw new HttpException(400, \Yii::t('app', 'Order ID Not Found.'));
        }
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = OrderDetail::find()
            ->where(['id' => $findId])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Order Detail ID Not Found.'));
        }

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
        $success                 = true;

        if ($query->save())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Order Detail Failed.'));
            } else {
                $success &= $query->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}