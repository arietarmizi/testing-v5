<?php

namespace api\forms\productbundle;


use api\components\BaseForm;
use common\models\ProductBundle;

class StoreProductBundleForm extends BaseForm
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
            ['price', 'double']
        ];
    }

    public function submit()
    {
        $query              = new ProductBundle();
        $query->name        = $this->name;
        $query->price       = $this->price;
        $query->description = $this->description;
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

        return ['productBundle' => $query];
    }


}