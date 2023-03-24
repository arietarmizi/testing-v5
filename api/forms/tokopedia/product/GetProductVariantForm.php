<?php


namespace api\forms\tokopedia\product;


use api\components\BaseForm;
use common\models\Provider;

class GetProductVariantForm extends BaseForm
{
    public $fsId;
    public $catId;
    public $_response;

    public function rules()
    {
        return [
            [['fsId', 'catId'], 'required']
        ];
    }

    public function submit()
    {
        $provider                = \Yii::$app->tokopediaProvider;
        $provider->_url          = 'inventory/v2/fs/' . $this->fsId . '/category/get_variant';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $provider->_query        = [
            'cat_id' => $this->catId,
        ];

        $this->_response = $provider->send();

        return true;
    }

    public function response()
    {
        return $this->_response;
    }
}