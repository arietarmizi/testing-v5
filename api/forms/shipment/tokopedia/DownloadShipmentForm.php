<?php


namespace api\forms\shipment\tokopedia;


use api\components\BaseForm;
use common\models\Provider;
use common\models\Shipment;
use common\models\ShipmentService;
use common\models\Shop;

class DownloadShipmentForm extends BaseForm {
    public $fsId;
    public $shopId;

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
            ['shopId', 'validateShop']
        ];
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
            $provider->_url           = 'v2/logistic/fs/' . $this->fsId . '/info';
            $provider->_query         = ['shop_id' => $this->_shop->marketplaceShopId];
            $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
            $this->_response          = $provider->send();
            $remoteShipments          = $this->_response['data'];

            foreach ($remoteShipments as $remoteShipment) {
                $remoteShipmentServices = $remoteShipment['services'];
                $this->_shipment        = Shipment::find()
                    ->where([
                        'shopId'                => $this->_shop->id,
                        'marketPlaceShipmentId' => (string)$remoteShipment['shipper_id']
                    ])->one();

                if (!$this->_shipment) {
                    $this->_shipment = new Shipment();
                }
                $this->_shipment->shopId                = $this->_shop->id;
                $this->_shipment->marketplaceShipmentId = $remoteShipment['shipper_id'];
                $this->_shipment->name                  = $remoteShipment['shipper_name'];
                $success                                &= $this->_shipment->save() && $this->_shipment->refresh();

                foreach ($remoteShipmentServices as $remoteShipmentService) {
                    $this->_shipmentService = ShipmentService::find()
                        ->where([
                            'shipmentId'                   => $this->_shipment->id,
                            'marketPlaceShipmentServiceId' => (string)$remoteShipmentService['service_id']
                        ])->one();

                    if (!$this->_shipmentService) {
                        $this->_shipmentService = new ShipmentService();
                    }

                    $this->_shipmentService->shipmentId                   = $this->_shipment->id;
                    $this->_shipmentService->marketplaceShipmentServiceId = $remoteShipmentService['service_id'];
                    $this->_shipmentService->name                         = $remoteShipmentService['service_name'];
                    $success                                              &= $this->_shipmentService->save() && $this->_shipmentService->refresh();
                }
            }

            $success ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        if ($success) {
            $this->getActiveCourier();
        }

        return $success;
    }

    public function getActiveCourier() {
        $db          = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $success     = true;

        try {
            /** @var Provider $provider */
            $provider                 = \Yii::$app->tokopediaProvider;
            $provider->_url           = 'v1/logistic/fs/' . $this->fsId . '/active-info';
            $provider->_query         = ['shop_id' => $this->_shop->marketplaceShopId];
            $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
            $this->_response          = $provider->send();
            $remoteActiveCouriers     = $this->_response['data']['Shops'];

            foreach ($remoteActiveCouriers as $remoteActiveCourier) {
                $remoteShipmentInfos = $remoteActiveCourier['ShipmentInfos'];
                foreach ($remoteShipmentInfos as $remoteShipmentInfo) {
                    $remoteActiveServices = $remoteShipmentInfo['ShipmentPackages'];
                    if ($remoteShipmentInfo['ShipmentAvailable'] == 1) {
                        $this->_shipment = Shipment::find()
                            ->where([
                                'shopId'                => $this->_shop->id,
                                'marketPlaceShipmentId' => (string)$remoteShipmentInfo['ShipmentID']
                            ])->one();

                        if ($this->_shipment) {
                            $this->_shipment->isAvailable = 1;
                            $success                      &= $this->_shipment->save() && $this->_shipment->refresh();
                        }
                    }
                    foreach ($remoteActiveServices as $remoteActiveService) {
                        if ($remoteActiveService['IsAvailable'] == 1) {
                            $this->_shipmentService = ShipmentService::find()
                                ->where([
                                    'shipmentId'                   => $this->_shipment->id,
                                    'marketplaceShipmentServiceId' => (string)$remoteActiveService['ShippingProductID']
                                ])->one();

                            if ($this->_shipmentService) {
                                $this->_shipmentService->isAvailable = 1;
                                $success                             &= $this->_shipmentService->save() && $this->_shipmentService->refresh();
                            }
                        }
                    }
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