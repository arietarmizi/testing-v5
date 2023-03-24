<?php


namespace api\forms\stockmanagement;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductVariant;
use common\models\StockManagement;
use common\models\Warehouse;

class UpdateStockManagementForm extends BaseForm
{
    public $warehouseId;
    public $productVariantId;
    public $promotionStock;
    public $orderedStock;
    public $availableStock;
    public $onHandStock;
    public $stockAlert;
    public $stockType;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['warehouseId', 'productVariantId', 'availableStock', 'onHandStock'], 'required'],
            [['promotionStock', 'orderedStock', 'availableStock', 'onHandStock', 'stockAlert'], 'double'],
            ['stockType', 'in', 'range' => array_keys(StockManagement::stocks())],
            ['warehouseId', 'validateWarehouse'],
            ['productVariantId', 'validateProductVariant'],
            ['status', 'in', 'range' => array_keys(StockManagement::statuses())]
        ];
    }

    public function validateWarehouse()
    {
        $warehouse = Warehouse::find()
            ->where(['id' => $this->warehouseId])
            ->andWhere(['status' => Warehouse::STATUS_ACTIVE])
            ->one();

        if (!$warehouse) {
            throw new HttpException(400, \Yii::t('app', 'Warehouse Not Found.'));
        }
    }

    public function validateProductVariant()
    {
        $productVariant = ProductVariant::find()
            ->where(['id' => $this->productVariantId])
            ->andWhere(['status' => ProductVariant::STATUS_ACTIVE])
            ->one();

        if (!$productVariant) {
            throw new HttpException(404, \Yii::t('app', 'Product Variant Not Found .'));
        }
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = StockManagement::find()
            ->where(['id' => $findId])
            ->one();

        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Stock Management Is Inactive.'));
        }
        $query->warehouseId      = $this->warehouseId;
        $query->productVariantId = $this->productVariantId;
        $query->promotionStock   = $this->promotionStock;
        $query->orderedStock     = $this->orderedStock;
        $query->availableStock   = $this->availableStock;
        $query->onHandStock      = $this->onHandStock;
        $query->stockAlert       = $this->stockAlert;
        $query->stockType        = $this->stockType ? $this->stockType : StockManagement::stocks();
        $query->status           = $this->status ? $this->status : StockManagement::statuses();

        $success = true;

        if ($query->validate())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Stock Management Failed.'));
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