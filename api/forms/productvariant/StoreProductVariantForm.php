<?php

namespace api\forms\productvariant;

use api\components\BaseForm;
use common\models\Product;
use common\models\ProductVariant;

class StoreProductVariantForm extends BaseForm
{
    public $id;
    public $sku;
    public $productId;
    public $name;
    public $sellingStatus;
    public $isShelfLife;
    public $duration;
    public $inboundLimit;
    public $outboundLimit;
    public $minOrder;
    public $description;
    public $productDescription;
    public $defaultPrice;
    public $length;
    public $width;
    public $height;
    public $weight;
    public $barcode;
    public $isPreOrder;
    public $minPreOrderDay;
    public $discount;
    public $isWholesale;
    public $isFreeReturn;
    public $isMustInsurance;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['sku', 'productId', 'name'], 'required'],
            [['name', 'description', 'productDescription', 'sellingStatus'], 'string'],
            [[
                'duration',
                'inboundLimit',
                'outboundLimit',
                'minOrder',
                'defaultPrice',
                'length',
                'width',
                'height',
                'weight',
                'minPreOrderDay',
                'discount',
            ], 'number'],
            [[
                'isShelfLife',
                'isPreOrder',
                'isWholesale',
                'isFreeReturn',
                'isMustInsurance'
            ], 'boolean'],
            ['productId', 'validateProductId']
        ];
    }

    public function validateProductId($attributes, $params)
    {
        $product = Product::find()
            ->where(['id' => $this->productId, 'status' => Product::STATUS_ACTIVE])
            ->one();
        if (!$product) {
            $this->addError($attributes, \Yii::t('app', 'Product ID ' . $this->productId . ' not found'));
        }

    }


    public function submit()
    {
        $query                     = new ProductVariant();
        $query->sku                = $this->sku;
        $query->productId          = $this->productId;
        $query->name               = $this->name;
        $query->productDescription = $this->productDescription;
        $query->description        = $this->description;
        $query->sellingStatus      = $this->sellingStatus;
        $query->duration           = $this->duration;
        $query->inboundLimit       = $this->inboundLimit;
        $query->outboundLimit      = $this->outboundLimit;
        $query->minOrder           = $this->minOrder;
        $query->defaultPrice       = $this->defaultPrice;
        $query->length             = $this->length;
        $query->width              = $this->width;
        $query->height             = $this->height;
        $query->weight             = $this->weight;
        $query->minPreOrderDay     = $this->minPreOrderDay;
        $query->discount           = $this->discount;
        $query->isShelfLife        = $this->isShelfLife;
        $query->isPreOrder         = $this->isPreOrder;
        $query->isWholesale        = $this->isWholesale;
        $query->isFreeReturn       = $this->isFreeReturn;
        $query->isMustInsurance    = $this->isMustInsurance;

        $query->save();
        $query->refresh();

        $this->_query = $query;
        return true;

    }

    public function response()
    {
        $response = $this->_query->toArray();

        unset($response['createdAt']);
        unset($response['updatedAt']);

        return ['productvariant' => $response];
    }


}