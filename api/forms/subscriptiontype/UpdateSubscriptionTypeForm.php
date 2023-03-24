<?php


namespace api\forms\subscriptiontype;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\SubscriptionType;

class UpdateSubscriptionTypeForm extends BaseForm
{
    public $id;
    public $name;
    public $duration;
    public $durationType;
    public $isSupportMultiple;
    public $transactionQuota;
    public $price;
    public $description;
    public $priority;
    public $status;

    private $_query;

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        return [
            [['name', 'duration', 'durationType', 'isSupportMultiple', 'transactionQuota', 'price'], 'required'],
            ['name', 'string', 'min' => 6],
            [['description'], 'string'],
            ['durationType', 'in', 'range' => array_keys(SubscriptionType::durations())],
            [['duration', 'price', 'priority'], 'number'],
            ['status', 'in', 'range' => array_keys(SubscriptionType::statuses())]
        ];
    }

    public function submit()
    {
        $getSubscriptionTypeId = \Yii::$app->request->get('id');

        $subscriptionType = SubscriptionType::find()
            ->where(['id' => $getSubscriptionTypeId])
            ->one();

        if (!$subscriptionType) {
            throw new HttpException(400, \Yii::t('app', 'Subscription Type Not Found'));
        }

        $subscriptionType->name              = $this->name;
        $subscriptionType->duration          = $this->duration;
        $subscriptionType->durationType      = $this->durationType;
        $subscriptionType->isSupportMultiple = $this->isSupportMultiple;
        $subscriptionType->transactionQuota  = $this->transactionQuota;
        $subscriptionType->price             = $this->price;
        $subscriptionType->description       = $this->description;
        $subscriptionType->priority          = $this->priority;
        $subscriptionType->status            = $this->status ? $this->status : SubscriptionType::statuses();

        $success = true;

        if ($subscriptionType->validate())
            if ($subscriptionType->hasErrors()) {
                $this->addError($subscriptionType->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Subscription Type Failed.'));
            } else {
                $success &= $subscriptionType->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}