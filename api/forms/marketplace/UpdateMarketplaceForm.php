<?php


namespace api\forms\marketplace;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\Marketplace;

class UpdateMarketplaceForm extends BaseForm
{
    public $code;
    public $marketplaceName;
    public $description;
    public $status;

    private $_marketplace;

    public function rules()
    {
        return [
            [['marketplaceName'], 'required'],
            [['marketplaceName', 'description'], 'string'],
            ['status', 'in', 'range' => array_keys(Marketplace::statuses())]
        ];
    }

    public function submit()
    {
        $findId = \Yii::$app->request->get('id');

        $marketplace = Marketplace::find()
            ->where(['id' => $findId])
            ->one();
        if (!$marketplace) {
            throw new HttpException(400, \Yii::t('app', 'Marketplace ID Not Found.'));
        }
        $marketplace->marketplaceName = $this->marketplaceName;
        $marketplace->description     = $this->description;
        $marketplace->status          = $this->status ? $this->status : array_keys(Marketplace::statuses());

        $success = true;

        if ($marketplace->save())
            if ($marketplace->hasErrors()) {
                $this->addError($marketplace->errors);
                throw new HttpException(400, \Yii::t('app', 'Update Marketplace Failed.'));
            } else {
                $success &= $marketplace->save();
            }
        return $success;
    }

    public function response()
    {
        return [];
    }
}