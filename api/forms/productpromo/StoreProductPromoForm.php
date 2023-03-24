<?php

namespace api\forms\productpromo;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductPromo;
use common\models\ProductVariant;

class StoreProductPromoForm extends BaseForm
{
    public $productVariantId;
    public $minQuantity;
    public $maxQuantity;
    public $defaultPrice;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['productVariantId', 'minQuantity', 'defaultPrice'], 'required'],
            [['minQuantity', 'maxQuantity', 'defaultPrice'], 'double'],
            ['productVariantId', 'validateProductVariant'],
        ];
    }

    public function validateProductVariant()
    {
        $query = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->andWhere(['status' => ProductVariant::STATUS_ACTIVE])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Promo ID Not Found.'));
        }
    }

    public function submit()
    {
        $query                   = new ProductPromo();
        $query->productVariantId = $this->productVariantId;
        $query->minQuantity      = $this->minQuantity;
        $query->maxQuantity      = $this->maxQuantity;
        $query->defaultPrice     = $this->defaultPrice;
        $query->save();
        $query->refresh();

        $this->_query = $query;
        return true;
    }

    public function response()
    {
        $query = $this->_query->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['productPromo' => $query];
    }
}