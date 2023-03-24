<?php


namespace api\forms\customer;


use api\components\BaseForm;
use common\models\Customer;
use common\validators\PhoneNumberValidator;

class StoreCustomerForm extends BaseForm
{
    public $customerName;
    public $email;
    public $phoneNumber;
    public $address;

    private $_customer;

    public function rules()
    {
        return [
            [['customerName', 'phoneNumber'], 'required'],
            [['customerName', 'address'], 'string'],
            ['email', 'email'],
            ['phoneNumber', PhoneNumberValidator::class],
        ];
    }

    public function submit()
    {
        $customer = new Customer();

        $customer->customerName = $this->customerName;
        $customer->email        = $this->email;
        $customer->phoneNumber  = $this->phoneNumber;
        $customer->address      = $this->address;

        $customer->save();
        $customer->refresh();

        $this->_customer = $customer;
        return true;
    }

    public function response()
    {
        $query = $this->_customer->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['customer' => $query];
    }
}