<?php


namespace api\forms\productbundle;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductBundle;

class UpdateProductBundleForm extends BaseForm
{
    public $id;
    public $name;
    public $price;
    public $description;
    public $status;

    private $_query;

    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['name', 'description'], 'string'],
            ['price', 'double'],
            ['status', 'in', 'range' => array_keys(ProductBundle::statuses())]
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $query = ProductBundle::find()
            ->where(['id' => $findId])
            ->one();

        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Product Bundle ID Not Found.'));
        }

        $query->name        = $this->name;
        $query->price       = $this->price;
        $query->description = $this->description;
        $query->status      = $this->status ? $this->status : ProductBundle::statuses();

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