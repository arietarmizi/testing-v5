<?php

namespace api\forms\warehouse;

use api\components\BaseForm;
use common\models\User;
use common\models\Warehouse;
use common\validators\PhoneNumberValidator;

class StoreWarehouseForm extends BaseForm
{
    public $shopId;
    public $name;
    public $description;
    public $subDistrictId;
    public $address;
    public $email;
    public $phoneNumber;
    public $whType;
    public $isDefault;
    public $latLon;
    public $latitude;
    public $longitude;
    public $branchShopSubscription;
    public $status;

    public $_warehouse;

    public function rules()
    {
        return [
            [['name', 'subDistrictId', 'email', 'phoneNumber'], 'required'],
            [['name', 'description', 'subDistrictId', 'address', 'email', 'latitude', 'longitude', 'latLon', 'shopId', 'whType'], 'string'],
            ['phoneNumber', PhoneNumberValidator::class],
            ['email', 'email'],
            ['branchShopSubscription', 'boolean']
        ];
    }

    public function submit()
    {
        $user = User::find()
            ->where([User::tableName() . '.id' => \Yii::$app->user->id])
            ->joinWith(['shop'])
            ->one();

        $transaction = \Yii::$app->db->beginTransaction();

        $warehouse                         = new Warehouse();
        $warehouse->shopId                 = $user->shop->marketplaceShopId;
        $warehouse->name                   = $this->name;
        $warehouse->description            = $this->description;
        $warehouse->subDistrictId          = $this->subDistrictId;
        $warehouse->address                = $this->address;
        $warehouse->email                  = $this->email;
        $warehouse->phoneNumber            = $this->phoneNumber;
        $warehouse->whType                 = $this->whType ? $this->whType : array_keys(Warehouse::warehouseTypes());
        $warehouse->isDefault              = $this->isDefault;
        $warehouse->latLon                 = $this->latLon;
        $warehouse->latitude               = $this->latitude;
        $warehouse->longitude              = $this->longitude;
        $warehouse->branchShopSubscription = $this->branchShopSubscription;

        $warehouse->save();
        if ($warehouse->save()) {
            $warehouse->refresh();
            $this->_warehouse = $warehouse;
            $transaction->commit();
            return true;
        } else {
            $this->addErrors($this->getErrors());
            $transaction->rollBack();
            return false;
        }
    }

    public function response()
    {
        $response = $this->_warehouse->toArray();

        unset($response['createdAt']);
        unset($response['updatedAt']);

        return ['warehouse' => $response];
    }
}