<?php


namespace api\forms\subscription;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Subscription;

class UpdateSubscriptionForm extends BaseForm
{
    public $userId;
    public $subscriptionTypeId;
    public $usedQuota;
    public $status;

    private $_subscriptionType;

    public function rules()
    {
        return [
            [['subscriptionTypeId'], 'required'],
            [['subscriptionTypeId', 'userId'], 'string'],
            [['usedQuota', 'remainingQuota'], 'double'],
            [['registerAt', 'expiredAt'], 'date'],
            ['status', 'in', 'range' => array_keys(Subscription::statuses())]
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $user = \Yii::$app->user->identity;

        $subscription = Subscription::find()
            ->where(['id' => $findId])
            ->one();

        $subscription->subscriptionTypeId = $this->subscriptionTypeId;
        $subscription->countSubscription();
        $subscription->usedQuota = $this->usedQuota;

        $success = true;

        if ($subscription->save())
            if ($subscription->hasErrors()) {
                $this->addError($subscription->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Subscription Failed.'));
            } else {
                $success &= $subscription->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }

}