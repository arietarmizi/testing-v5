<?php

namespace api\forms\subscriptiontype;


use api\components\BaseForm;
use common\models\SubscriptionType;

class StoreSubscriptionTypeForm extends BaseForm
{
    public $name;
    public $duration;
    public $durationType;
    public $isSupportMultiple;
    public $transactionQuota;
    public $price;
    public $description;
    public $priority;

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
        ];
    }

    public function submit()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $query                    = new SubscriptionType();
        $query->name              = $this->name;
        $query->duration          = $this->duration;
        $query->durationType      = $this->durationType;
        $query->isSupportMultiple = $this->isSupportMultiple;
        $query->transactionQuota  = $this->transactionQuota;
        $query->price             = $this->price;
        $query->description       = $this->description;
        $query->priority          = $this->priority;

        if ($query->save()) {
            $query->refresh();
            $this->_query = $query;
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
        $query = $this->_query->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['user' => $query];
    }
}