<?php


namespace api\forms\customer;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Customer;
use common\validators\PhoneNumberValidator;

class UpdateCustomerForm extends BaseForm
{
    public $customerName;
    public $email;
    public $phoneNumber;
    public $address;
    public $status;

    private $_customer;

    public function rules()
    {
        return [
            [['customerName', 'phoneNumber'], 'required'],
            [['customerName', 'address'], 'string'],
            ['email', 'email'],
            ['phoneNumber', PhoneNumberValidator::class],
            ['status', 'in', 'range' => array_keys(Customer::statuses())]
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $customer = Customer::find()
            ->where(['id' => $findId])
            ->one();
        if (!$customer) {
            throw new HttpException(400, \Yii::t('app', 'Customer ID Not Found.'));
        }
        $customer->customerName = $this->customerName;
        $customer->email        = $this->email;
        $customer->phoneNumber  = $this->phoneNumber;
        $customer->address      = $this->address;
        $customer->status       = $this->status ? $this->status : array_keys(Customer::statuses());

        $success = true;

        if ($customer->save())
            if ($customer->hasErrors()) {
                $this->addError($customer->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Customer Failed.'));
            } else {
                $success &= $customer->save();
            }
        return $success;
    }

    public function response()
    {
        return[];
    }
}