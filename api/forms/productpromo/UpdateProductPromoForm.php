<?php


namespace api\forms\productpromo;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductPromo;
use common\models\ProductVariant;

class UpdateProductPromoForm extends BaseForm
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
            ['status', 'in', 'range' => array_keys(ProductPromo::statuses())]
        ];
    }

    public function validateProductVariant()
    {
        $query = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->andWhere(['status' => ProductVariant::STATUS_ACTIVE])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product variant ID Not Found.'));
        }
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = ProductPromo::find()
            ->where(['id' => $findId])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Promo ID Not Found!.'));
        }

        $query->productVariantId = $this->productVariantId;
        $query->minQuantity      = $this->minQuantity;
        $query->maxQuantity      = $this->maxQuantity;
        $query->defaultPrice     = $this->defaultPrice;
        $query->status           = $this->status ? $this->status : ProductPromo::statuses();

        $success = true;

        if ($query->save())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Product Promo Failed.'));
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