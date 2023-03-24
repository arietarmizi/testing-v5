<?php


namespace api\forms\tokopedia\product;


use api\components\BaseForm;
use api\config\ApiCode;
use common\models\Product;
use common\models\Provider;
use GuzzleHttp\Exception\ClientException;

class GetInfoByIdForm extends BaseForm
{
    public  $fsId;
    public  $productId;
    public  $shopId;
    public  $name;
    public  $sku;
    private $_response;

    public function rules()
    {
        return [
            [['fsId'], 'required'],
            ['productId', 'number'],
            [['productId', 'shopId'], 'number'],
            [['name'], 'string']
        ];
    }

    public function submit()
    {
        $productId = Product::find()
            ->where(['id' => $this->productId])
            ->one();

        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'inventory/v1/fs/' . $this->fsId . '/product/info';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $provider->_query         = [
            'product_id' => $this->productId,
//            'sku'        => $this->sku
        ];
        $this->_response          = $provider->send();
        $product         = new Product();
        $product->id     = $this->productId;
        $product->shopId = $this->shopId;
        $product->name   = $this->name;
        $product->save();
        return true;
    }

    public function response()
    {
        return $this->_response;
    }

}