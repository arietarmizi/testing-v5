<?php

namespace api\forms\tokopedia\order;


use api\components\BaseForm;
use api\components\HttpException;
use common\encryption\Tokopedia;
use common\models\Provider;
use GuzzleHttp\Exception\ClientException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class GetSingleOrderForm extends BaseForm
{
    public  $fsId        = '15394';
    public  $invoice_nums = 'INV/20210802/MPL/1463471138';
    private $_response;
    public $invoiceNo;


    public function rules()
    {
        return [
            ['invoiceNo', 'string']
        ];
    }

    public function submit()
    {

        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'v2/fs/' . $this->fsId . '/order';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $provider->_query         = [
            'invoice_num' => $this->invoiceNo,
        ];

        if ($result = $provider->send()) {
            $data       = ArrayHelper::getValue($result, 'data', []);
            $encryption = ArrayHelper::getValue($data, 'encryption');

            if ($encryption) {
                $secret  = ArrayHelper::getValue($encryption, 'secret');
                $content = ArrayHelper::getValue($encryption, 'content');
                try {
                    $decryptedContent = Tokopedia::decryptContent($secret, $content);
                    if ($decryptedContent) {
                        $data = ArrayHelper::merge($data, $decryptedContent);
                        unset($data['encryption']);
                    }
                } catch (\Exception $e) {

                }
            }
            $this->_response = $data;
            return true;
        }
        return false;
    }

    public function response()
    {
        return $this->_response;
    }
}