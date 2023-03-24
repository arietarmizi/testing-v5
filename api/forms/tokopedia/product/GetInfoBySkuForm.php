<?php


namespace api\forms\tokopedia\product;


use api\components\BaseForm;
use common\models\Provider;

class GetInfoBySkuForm extends BaseForm
{
    public $fsId;
    public $sku;
    public $_response;

    public function rules()
    {
        return [
            [['fsId', 'sku'], 'required']
        ];
    }

    public function submit()
    {
        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'inventory/v1/fs/' . $this->fsId . '/product/info';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $provider->_query         = [
            'sku' => $this->sku
        ];
        $this->_response          = $provider->send();
        return true;
    }

    public function response()
    {
        return $this->_response;
    }
}