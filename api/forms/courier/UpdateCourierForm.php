<?php


namespace api\forms\courier;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\CourierInformation;
use common\validators\PhoneNumberValidator;

class UpdateCourierForm extends BaseForm
{
    public $marketplaceCourierId;
    public $courierName;
    public $phoneNumber;
    public $notes;
    public $status;

    private $_courier;

    public function rules()
    {
        return [
            [['marketplaceCourierId', 'courierName', 'notes'], 'string'],
            ['phoneNumber', PhoneNumberValidator::class],
            ['status', 'in', 'range' => array_keys(CourierInformation::statuses())]
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $courier = CourierInformation::find()
            ->where(['id' => $findId])
            ->one();
        if (!$courier) {
            throw new HttpException(400, \Yii::t('app', 'Courier ID Not Found.'));
        }
        $courier->marketplaceCourierId = $this->marketplaceCourierId;
        $courier->courierName          = $this->courierName;
        $courier->phoneNumber          = $this->phoneNumber;
        $courier->notes                = $this->notes;
        $courier->status               = $this->status ? $this->status : array_keys(CourierInformation::statuses());

        $success = true;

        if ($courier->save())
            if ($courier->hasErrors()) {
                $this->addError($courier->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Courier Failed.'));
            } else {
                $success &= $courier->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}