<?php

namespace api\forms\productbundledetail;

use api\components\BaseForm;
use common\models\ProductBundle;
use common\models\ProductBundleDetail;
use common\models\ProductVariant;

class StoreProductBundleDetailForm extends BaseForm
{
    public $productBundleId;
    public $productVariantId;
    public $quantity;

    private $_query;

    public function rules()
    {
        return [
            [['productBundleId', 'productVariantId', 'quantity'], 'required'],
            ['quantity', 'double'],
            ['productVariantId', 'validateProductVariant'],
            ['productBundleId', 'validateProductBundle']
        ];
    }

    public function validateProductBundle($attribute, $params)
    {
        $query = ProductBundle::find()
            ->where(['id' => $this->productBundleId])
            ->andWhere(['status' => ProductBundle::STATUS_ACTIVE])
            ->one();

        if (!$query) {
            $this->addError($attribute, \Yii::t('app', '{attribute} "{value}" Not Found.!', [
                'attribute' => $attribute,
                'value'     => $this->productBundleId
            ]));
        }
    }

    public function validateProductVariant($attribute, $params)
    {
        $query = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->andWhere(['status' => ProductVariant::STATUS_ACTIVE])
            ->one();
        if (!$query) {
            $this->addError($attribute, \Yii::t('app', '{attribute} "{value}" is inactive.', [
                'attribute' => $attribute,
                'value'     => $this->productVariantId
            ]));
        }
    }

    public function submit()
    {
        $query = new ProductBundleDetail();

        $query->productBundleId  = $this->productBundleId;
        $query->productVariantId = $this->productVariantId;
        $query->quantity         = $this->quantity;
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

        return ['productBundleDetail' => $query];
    }
}