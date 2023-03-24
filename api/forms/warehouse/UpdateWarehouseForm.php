<?php

namespace api\forms\warehouse;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\User;
use common\models\Warehouse;
use common\validators\PhoneNumberValidator;

class UpdateWarehouseForm extends BaseForm
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
            ['branchShopSubscription', 'boolean'],
            ['isDefault', 'boolean'],
            ['status', 'in', 'range' => array_keys(Warehouse::statuses())],
            ['whType', 'in', 'range' => array_keys(Warehouse::warehouseTypes())],
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $user = User::find()
            ->where([User::tableName() . '.id' => \Yii::$app->user->id])
            ->joinWith(['shop'])
            ->one();

        $warehouse = Warehouse::find()
            ->where(['id' => $findId])
            ->one();
        if (!$warehouse) {
            throw new HttpException(400, \Yii::t('app', 'Warehouse ID Not Found.'));
        }

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
        $warehouse->status                 = $this->status ? $this->status : array_keys(Warehouse::statuses());

        $success = true;

        if ($warehouse->save())
            if ($warehouse->hasErrors()) {
                $this->addError($warehouse->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Warehouse Failed.'));
            } else {
                $success &= $warehouse->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}