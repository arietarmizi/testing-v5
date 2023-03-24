<?php


namespace api\forms\subscription;


use api\components\BaseForm;
use common\models\Subscription;

class StoreSubscriptionForm extends BaseForm
{
    public $userId;
    public $subscriptionTypeId;
    public $status;

    private $_subscriptionType;

    public function rules()
    {
        return [
            [['subscriptionTypeId'], 'required'],
            [['subscriptionTypeId', 'userId'], 'string'],
            [['usedQuota', 'remainingQuota'], 'double'],
            [['registerAt', 'expiredAt'], 'date'],
        ];
    }

    public function submit()
    {
        $user = \Yii::$app->user->identity;

        $subscription                     = new Subscription();
        $subscription->userId             = $user->id;
        $subscription->subscriptionTypeId = $this->subscriptionTypeId;
        $subscription->countSubscription();
        $subscription->usedQuota = 0;

        $subscription->save();
        $subscription->refresh();

        $this->_subscriptionType = $subscription;
        return true;
    }

    public function response()
    {
        $query = $this->_subscriptionType->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['subscriptionType' => $query];
    }
}