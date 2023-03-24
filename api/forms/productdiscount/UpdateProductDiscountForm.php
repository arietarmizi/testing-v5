<?php


namespace api\forms\productdiscount;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductDiscount;
use common\models\ProductVariant;

class UpdateProductDiscountForm extends BaseForm
{
    public $productVariantId;
    public $discountPrice;
    public $discountPercentage;
    public $startTime;
    public $endTime;
    public $initialQuota;
    public $remainingQuota;
    public $maxOrder;
    public $slashPriceStatusId;
    public $useWarehouse;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['startTime', 'endTime'], 'required'],
            [['productVariantId', 'slashPriceStatusId'], 'string'],
            [['discountPrice', 'discountPercentage', 'initialQuota', 'remainingQuota', 'maxOrder'], 'double'],
            ['useWarehouse', 'boolean'],
            ['productVariantId', 'validateProductVariant'],
            ['status', 'in', 'range' => array_keys(ProductDiscount::statuses())]
        ];
    }

    public function validateProductVariant()
    {
        $query = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->andWhere(['status' => ProductVariant::STATUS_ACTIVE])
            ->one();

        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Variant Not Found.'));
        }
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = ProductDiscount::find()
            ->where(['id' => $findId])
            ->one();

        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Discount ID Not Found.'));
        }

        $query->productVariantId   = $this->productVariantId;
        $query->discountPrice      = $this->discountPrice;
        $query->discountPercentage = $this->discountPercentage;
        $query->startTime          = $this->startTime;
        $query->endTime            = $this->endTime;
        $query->initialQuota       = $this->initialQuota;
        $query->remainingQuota     = $this->remainingQuota;
        $query->maxOrder           = $this->maxOrder;
        $query->slashPriceStatusId = $this->slashPriceStatusId;
        $query->useWarehouse       = $this->useWarehouse;
        $query->status             = $this->status ? $this->status : ProductDiscount::statuses();

        $success = true;

        if ($query->save())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Product Discount Failed.'));
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