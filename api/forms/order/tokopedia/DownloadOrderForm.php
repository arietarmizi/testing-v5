<?php


namespace api\forms\order\tokopedia;


use api\components\BaseForm;
use api\components\HttpException;
use Carbon\Carbon;
use common\models\Customer;
use common\models\MasterStatus;
use common\models\Order;
use common\models\OrderDetail;
use common\models\OrderStatus;
use common\models\Product;
use common\models\ProductVariant;
use common\models\Provider;
use common\models\Shipment;
use common\models\ShipmentService;
use common\models\Shop;

class DownloadOrderForm extends BaseForm {
    public $fsId;
    public $fromDate;
    public $toDate;
    public $shopId;

    public $_response;

    private $_page = 1;
    private $_perPage = 10;

    /** @var Shop */
    private $_shop;

    /** @var Order */
    private $_order;

    /** @var OrderDetail */
    private $_orderDetail;

    /** @var Customer */
    private $_customer;

    /** @var Shipment */
    private $_shipment;

    /** @var ShipmentService */
    private $_shipmentService;

    /** @var ProductVariant */
    private $_productVariant;

    /** @var OrderStatus */
    private $_orderStatus;

    public function rules() {
        return [
            [['fsId', 'shopId'], 'required'],
            [['shopId', 'fsId'], 'number'],
            [['fromDate', 'toDate'], 'date', 'format' => 'php:Y-m-d'],
            ['shopId', 'validateShop'],
            ['fromDate', 'validateDate']
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

    public function validateDate($attributes, $param = []) {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate   = Carbon::parse($this->toDate);
        $interval = $toDate->diffInDays($fromDate);

        if ($interval > 3) {
            $this->addError($attributes,
                \Yii::t('app', 'Can pull order with 3 days interval between fromDate and toDate'));
        }
    }

    public function init() {
        parent::init();
    }

    public function getAllOrders($page, $perPage) {
        $unixFromDate = Carbon::parse($this->fromDate . ' 00:00:00.000')->timestamp;
        $unixToDate   = Carbon::parse($this->toDate . ' 00:00:00.000')->timestamp;
        /** @var Provider $provider */
        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'v2/order/list';
        $provider->_query         = [
            'fs_id'     => $this->fsId,
            'from_date' => $unixFromDate,
            'to_date'   => $unixToDate,
            'page'      => $page,
            'per_page'  => $perPage,
            'shop_id'   => $this->shopId
        ];
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $response                 = $provider->send();
        return $response['data'];
    }

    public function getSingleOrder($order_id) {
        /** @var Provider $provider */
        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'v2/fs/' . $this->fsId . '/order';
        $provider->_query         = [
            'order_id' => $order_id
        ];
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;
        $response                 = $provider->send();
        return $response['data'];
    }

    public function getCustomer($remoteBuyerInfo = []) {
        $customer = Customer::find()
            ->where(['marketplaceCustomerId' => $remoteBuyerInfo['buyer_id']])
            ->one();

        if (!$customer) {
            $customer = new Customer();
        }
        $customer->marketplaceCustomerId = $remoteBuyerInfo['buyer_id'];
        $customer->customerName          = $remoteBuyerInfo['buyer_fullname'];
        $customer->email                 = $remoteBuyerInfo['buyer_email'];
        $customer->phoneNumber           = $remoteBuyerInfo['buyer_phone'];
        $customer->save() && $customer->refresh();

        return $customer;
    }

    public function getShipment($marketplaceShipmentId) {
        $shipment = Shipment::find()
            ->where([
                'shopId'                => $this->_shop->id,
                'marketplaceShipmentId' => (string)$marketplaceShipmentId
            ])->one();

        if (!$shipment) {
            throw new HttpException(400, \Yii::t('app', 'Shipment not found, you need to sync the shipment first.'));
        }

        return $shipment;
    }

    public function getShipmentService($shipmentId, $marketplaceShipmentServiceId) {
        $shipmentService = ShipmentService::find()
            ->where([
                'shipmentId'                   => $shipmentId,
                'marketplaceShipmentServiceId' => $marketplaceShipmentServiceId
            ])->one();

        if (!$shipmentService) {
            throw new HttpException(400,
                \Yii::t('app', 'Shipment service not found, you need to sync the shipment first.'));
        }

        return $shipmentService;
    }

    public function getProductVariant($marketplaceProductVariantId) {
        $productVariant = ProductVariant::find()
            ->joinWith(['product'])
            ->where([
                Product::tableName() . '.shopId'                             => $this->_shop->id,
                ProductVariant::tableName() . '.marketplaceProductVariantId' => (string)$marketplaceProductVariantId
            ])->one();

        if (!$productVariant) {
            throw new HttpException(400,
                \Yii::t('app', 'Product variant not found, you need to sync the product first.'));
        }

        return $productVariant;
    }

    public function getOrderStatus($marketplaceOrderStatus) {
        $orderStatus = OrderStatus::find()
            ->where([
                'marketplaceId'         => $this->_shop->marketplaceId,
                'marketplaceStatusCode' => $marketplaceOrderStatus
            ])->one();

        if (!$orderStatus) {
            throw new HttpException(400,
                \Yii::t('app', 'Sorry, order status is not available.'));
        }
        return $orderStatus;
    }

    public function saveOrders($page, $perPage) {
        $remoteOrders = $this->getAllOrders($page, $perPage);
        if ($remoteOrders != null) {
            foreach ($remoteOrders as $remoteOrder) {
                $remoteSingleOrder  = $this->getSingleOrder($remoteOrder['order_id']);
                $remoteOrderDetails = $remoteSingleOrder['order_info']['order_detail'];

                $this->_customer        = $this->getCustomer($remoteSingleOrder['buyer_info']);
                $this->_shipment        = $this->getShipment($remoteSingleOrder['order_info']['shipping_info']['shipping_id']);
                $this->_shipmentService = $this->getShipmentService(
                    $this->_shipment->id,
                    $remoteSingleOrder['order_info']['shipping_info']['sp_id']
                );
                $this->_orderStatus     = $this->getOrderStatus($remoteSingleOrder['order_status']);

                $this->_order = Order::find()
                    ->where(['refInv' => $remoteOrder['invoice_ref_num']])
                    ->one();

                if (!$this->_order) {
                    $this->_order = new Order();
                }

                $this->_order->orderDate         = Carbon::parse($remoteSingleOrder['create_time'])
                    ->setTimezone('UTC')->format('Y-m-d H:i:s');
                $this->_order->refInv            = $remoteSingleOrder['invoice_number'];
                $this->_order->customerId        = $this->_customer->id;
                $this->_order->shipmentId        = $this->_shipment->id;
                $this->_order->shipmentServiceId = $this->_shipmentService->id;
                $this->_order->orderStatusId     = $this->_orderStatus->id;
                $this->_order->save() && $this->_order->refresh();

                if (!empty($this->_order->orderDetails)) {
                    OrderDetail::deleteAll(['orderId' => $this->_order->id]);
                }

                foreach ($remoteOrderDetails as $remoteOrderDetail) {
                    $this->_productVariant = $this->getProductVariant($remoteOrderDetail['product_id']);

                    $this->_orderDetail                   = new OrderDetail();
                    $this->_orderDetail->orderId          = $this->_order->id;
                    $this->_orderDetail->productVariantId = $this->_productVariant->id;
                    $this->_orderDetail->quantity         = $remoteOrderDetail['quantity'];
                    $this->_orderDetail->weight           = $remoteOrderDetail['weight'];
                    $this->_orderDetail->totalWeight      = $remoteOrderDetail['total_weight'];
                    $this->_orderDetail->isFreeReturn     = $remoteOrderDetail['is_free_returns'];
                    $this->_orderDetail->productPrice     = $remoteOrderDetail['product_price'];
                    $this->_orderDetail->insurancePrice   = $remoteOrderDetail['insurance_price'];
                    $this->_orderDetail->subTotalPrice    = $remoteOrderDetail['subtotal_price'];
                    $this->_orderDetail->save() && $this->_orderDetail->refresh();
                }
            }
            $this->_page = $this->_page + 1;
            $this->saveOrders($this->_page, $this->_perPage);
        }
    }

    public function submit() {
        $this->saveOrders($this->_page, $this->_perPage);
        return true;
    }

    public function response() {
        return [];
    }
}