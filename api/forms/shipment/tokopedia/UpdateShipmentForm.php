<?php


namespace api\forms\shipment\tokopedia;


use api\components\BaseForm;
use common\models\Provider;
use common\models\Shipment;
use common\models\ShipmentService;
use common\models\Shop;
use yii\base\Object;

class UpdateShipmentForm extends BaseForm {
    public $fsId;
    public $shopId;
    public $request = [];

    /** @var object */
    private $_response;

    /** @var Shop */
    private $_shop;

    /** @var Shipment */
    private $_shipment;

    /** @var ShipmentService */
    private $_shipmentService;

    public function rules() {
        return [
            [['fsId', 'shopId'], 'required'],
            [['shopId', 'fsId'], 'number'],
            ['request', 'validateRequest'],
            ['shopId', 'validateShop']
        ];
    }

    public function validateRequest($attributes, $param = []) {
        if (!is_array($this->$attributes)) {
            $this->addError($attributes, \Yii::t('app', '{attribute} is not array', ['attribute' => $attributes]));
        }
    }

    public function validateShop($attributes, $param = []) {
        $this->_shop = Shop::find()
            ->where([
                'marketplaceShopId' => $this->shopId,
                'fsId'              => $this->fsId
            ])->one();

        if (!$this->_shop) {
            $this->addError($attributes, \Yii::t('app', '{shopId} is not registered', ['shopId' => $this->shopId]));
        }
    }

    public function init() {
        parent::init();
    }

    public function submit() {
        $transaction = \Yii::$app->db->beginTransaction();
        $success     = true;

        try {
            /** @var Provider $provider */
            $provider                 = \Yii::$app->tokopediaProvider;
            $provider->_url           = 'v2/logistic/fs/' . $this->fsId . '/update';
            $provider->_query         = ['shop_id' => $this->_shop->marketplaceShopId];
            $provider->_requestBody   = $this->request;
            $provider->_requestMethod = Provider::REQUEST_METHOD_POST;
            $this->_response          = $provider->send();

            foreach ($this->request as $marketplaceShipmentId => $marketplaceShipmentServices) {
                $isAvailableCount = 0;
                foreach ($marketplaceShipmentServices as $marketplaceShipmentServiceId => $isAvailable) {
                    $this->_shipmentService = ShipmentService::find()
                        ->joinWith(['shipment'])
                        ->where([
                            Shipment::tableName() . '.marketplaceShipmentId'               => $marketplaceShipmentId,
                            ShipmentService::tableName() . '.marketplaceShipmentServiceId' => $marketplaceShipmentServiceId
                        ])->one();

                    if ($this->_shipmentService) {
                        $this->_shipmentService->isAvailable = $isAvailable;
                        $success                             &= $this->_shipmentService->save() && $this->_shipmentService->refresh();
                    }

                    if ($isAvailable == true) {
                        $isAvailableCount++;
                    }
                }

                $this->_shipment = Shipment::find()
                    ->where([
                        'shopId'                => $this->_shop->id,
                        'marketplaceShipmentId' => $marketplaceShipmentId
                    ])->one();

                if ($this->_shipment) {
                    $this->_shipment->isAvailable = $isAvailableCount > 0;
                    $success                      &= $this->_shipment->save() && $this->_shipment->refresh();
                }
            }
            $success ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        return $success;
    }

    public function response() {
        return [];
    }
}