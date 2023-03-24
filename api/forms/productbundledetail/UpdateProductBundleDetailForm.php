<?php


namespace api\forms\productbundledetail;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductBundle;
use common\models\ProductBundleDetail;
use common\models\ProductVariant;

class UpdateProductBundleDetailForm extends BaseForm
{
    public $productBundleId;
    public $productVariantId;
    public $quantity;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['productBundleId', 'productVariantId', 'quantity'], 'required'],
            ['quantity', 'double'],
            ['productVariantId', 'validateProductVariant'],
            ['productBundleId', 'validateProductBundle'],
            ['status', 'in', 'range' => array_keys(ProductBundleDetail::statuses())]
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
        $findId = \Yii::$app->request->get('id');

        $query = ProductBundleDetail::find()
            ->where(['id' => $findId])
            ->one();

        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Bundle Detail ID Not Found.'));
        }

        $query->productBundleId  = $this->productBundleId;
        $query->productVariantId = $this->productVariantId;
        $query->quantity         = $this->quantity;
        $query->status           = $this->status ? $this->status : ProductBundleDetail::statuses();

        $success = true;

        if ($query->save())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Product Bundle Detail Failed.'));
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