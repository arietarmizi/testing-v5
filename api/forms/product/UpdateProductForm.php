<?php


namespace api\forms\product;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Product;

class UpdateProductForm extends BaseForm
{
    public $id;
    public $shopId;
    public $productSubCategoryId;
    public $code;
    public $name;
    public $condition;
    public $minOrder;
    public $productDescription;
    public $isMaster;
    public $description;
    public $status;

    /** @var Product */
    protected $_product;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'description', 'condition', 'productDescription'], 'string'],
            [['id', 'shopId', 'productSubCategoryId', 'minOrder', 'isMaster'], 'number'],
            ['id', 'validateProduct'],
            ['status', 'in', 'range' => array_keys(Product::statuses())]
        ];
    }

    public function validateProduct()
    {
        $productId = \Yii::$app->request->get('id');

        $product = Product::find()
            ->where(['id' => $productId])
            ->andWhere(['status' => Product::STATUS_ACTIVE])
            ->one();
        if (!$productId) {
            throw new HttpException(400, \Yii::t('app', 'Product ID not Found.'));
        }
    }


    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = Product::find()
            ->where(['id' => $findId])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product ID Not Found.'));
        }

        $query->productSubCategoryId = $this->productSubCategoryId;
        $query->name                 = $this->name;
        $query->condition            = $this->condition;
        $query->minOrder             = $this->minOrder;
        $query->productDescription   = $this->productDescription;
        $query->description          = $this->description;
        $query->isMaster             = $this->isMaster;
        $query->status               = $this->status ? $this->status : Product::statuses();

        $success = true;

        if ($query->save())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Product Failed.'));
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