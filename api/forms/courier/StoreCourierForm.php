<?php


namespace api\forms\courier;


use api\components\BaseForm;
use common\models\CourierInformation;
use common\validators\PhoneNumberValidator;

class StoreCourierForm extends BaseForm
{
    public $marketplaceCourierId;
    public $courierName;
    public $phoneNumber;
    public $notes;

    private $_courier;

    public function rules()
    {
        return [
            [['marketplaceCourierId', 'courierName', 'notes'], 'string'],
            ['phoneNumber', PhoneNumberValidator::class]
        ];
    }

    public function submit()
    {
        $courier              = new CourierInformation();
        $courier->courierName = $this->courierName;
        $courier->phoneNumber = $this->phoneNumber;
        $courier->notes       = $this->notes;

        $courier->save();
        $courier->refresh();

        $this->_courier = $courier;
        return true;
    }

    public function response()
    {
        $query = $this->_courier->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['courierInformation' => $query];
    }
}