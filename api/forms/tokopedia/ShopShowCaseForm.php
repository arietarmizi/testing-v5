<?php

namespace api\forms\tokopedia;

use api\components\BaseForm;
use common\models\Provider;

class ShopShowCaseForm extends BaseForm
{
    public  $fsId    = '15394';
    public  $shop_id = '11960781';
    private $_response;

    public function rules()
    {
        return [];
    }

    public function submit()
    {
        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'v1/showcase/fs/' . $this->fsId . '/get';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $provider->_query         = [
            'shop_id' => $this->shop_id
        ];
        $this->_response          = $provider->send();
        return true;
    }

    public function response()
    {
        return $this->_response;
    }
}
