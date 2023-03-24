<?php

namespace api\forms\masterstatus;

use api\components\BaseForm;
use common\models\Marketplace;
use common\models\MasterStatus;

class StoreMasterStatusForm extends BaseForm
{
    public $id;
    public $marketplaceId;
    public $statusCode;
    public $desc;

    private $_query;

    public function rules()
    {
        return [
            [['marketplaceId', 'statusCode', 'desc'], 'required'],
            [['marketplaceId', 'desc'], 'string'],
            [['desc'], 'string'],
            ['marketplaceId', 'validateMarketplace'],
            ['statusCode', 'validateStatusCode']

        ];
    }

    public function validateMarketplace($attributes, $params)
    {
        $marketplace = Marketplace::find()
            ->where(['id' => $this->marketplaceId, 'status' => Marketplace::STATUS_ACTIVE])
            ->one();
        if (!$marketplace) {
            $this->addError($attributes, 'id' . $this->marketplaceId . 'not found or its inactive status.');
        }
    }

    public function validateStatusCode($attributes, $params)
    {
        $statusCode = MasterStatus::find()
            ->where(['statusCode' => $this->statusCode])
            ->one();
        if ($statusCode) {
            $this->addError($attributes, 'statusCode' . $this->statusCode . 'has been added.');
        }
    }

    public function submit()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $query                = new MasterStatus();
        $query->marketplaceId = $this->marketplaceId;
        $query->statusCode    = $this->statusCode;
        $query->desc          = $this->desc;

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

        return ['masterStatus' => $query];
    }
}