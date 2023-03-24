<?php


namespace api\forms\shop;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Marketplace;
use common\models\Shop;

class UpdateShopForm extends BaseForm
{
    public $marketplaceShopId;
    public $marketplaceId;
    public $fsId;
    public $userId;
    public $shopName;
    public $description;
    public $isOpen;
    public $status;

    private $_shop;

    public function rules()
    {
        return [
            [['marketplaceShopId', 'fsId', 'marketplaceId', 'shopName'], 'required'],
            [['fsId', 'userId', 'shopName', 'description'], 'string'],
            ['isOpen', 'in', 'range' => array_keys(Shop::openStatuses())],
            ['status', 'in', 'range' => array_keys(Shop::statuses())]
        ];
    }

    public function validatemarketplace()
    {
        $marketplace = Marketplace::find()
            ->where(['id' => $this->marketplaceId])
            ->andWhere(['status' => Marketplace::STATUS_ACTIVE])
            ->one();
        if (!$marketplace) {
            throw new HttpException(400, \Yii::t('app', 'Marketplace ID Not Found.'));
        }
    }

    public function submit()
    {
        $shopId = \Yii::$app->request->get('id');

        $user = \Yii::$app->user->identity;

        $shop = Shop::find()
            ->where(['id' => $shopId])
            ->one();
        if (!$shop) {
            throw new HttpException(400, \Yii::t('app', 'Shop ID Not Found.'));
        }

        $shop->marketplaceShopId = $this->marketplaceShopId;
        $shop->marketplaceId     = $this->marketplaceId;
        $shop->fsId              = $this->fsId;
        $shop->userId            = $user->id;
        $shop->shopName          = $this->shopName;
        $shop->description       = $this->description;
        $shop->isOpen            = $this->isOpen ? $this->isOpen : Shop::openStatuses();
        $shop->status            = $this->status ? $this->status : Shop::statuses();

        $success = true;

        if ($shop->save())
            if ($shop->hasErrors()) {
                $this->addError($shop->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Shop Failed.'));
            } else {
                $success &= $shop->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}