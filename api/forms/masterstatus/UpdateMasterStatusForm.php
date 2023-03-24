<?php


namespace api\forms\masterstatus;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Marketplace;
use common\models\MasterStatus;

class UpdateMasterStatusForm extends BaseForm
{
    public $id;
    public $marketplaceId;
    public $statusCode;
    public $desc;
    public $status;

    public function rules()
    {
        return [
            [['marketplaceId', 'statusCode', 'desc'], 'required'],
            [['marketplaceId', 'desc'], 'string'],
            [['desc'], 'string'],
            ['status', 'in', 'range' => array_keys(MasterStatus::statuses())],
            ['marketplaceId', 'validateMarketplace'],
//            ['statusCode', 'validateStatusCode']

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
            $this->addError($attributes, 'statusCode ' . $this->statusCode . ' has been added.');
        }
    }

    public function submit()
    {
        $masterStatusId = \Yii::$app->request->get('id');

        $query = MasterStatus::find()
            ->where(['id' => $masterStatusId])
            ->one();
        if (!$query) {
            throw new HttpException(400, \Yii::t('app', 'Marketplace ID Not Found.'));
        }

        $query->marketplaceId = $this->marketplaceId;
        $query->statusCode    = $this->statusCode;
        $query->desc          = $this->desc;
        $query->status        = $this->status ? $this->status : MasterStatus::statuses();

        $success = true;

        if ($query->validate())
            if ($query->hasErrors()) {
                $this->addError($query->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Master Status Failed.'));
            } else {
                $success &= $query->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}