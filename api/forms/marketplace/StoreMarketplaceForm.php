<?php

namespace api\forms\marketplace;

use api\components\BaseForm;
use common\models\Marketplace;

class StoreMarketplaceForm extends BaseForm
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
            [['marketplaceName', 'description'], 'string']
        ];
    }

    public function submit()
    {
        $marketplace                  = new Marketplace();
        $marketplace->marketplaceName = $this->marketplaceName;
        $marketplace->description     = $this->description;
        $marketplace->generateCode();

        $marketplace->save();
        $marketplace->refresh();

        $this->_marketplace = $marketplace;
        return true;
    }

    public function response()
    {
        $query = $this->_marketplace->toArray();

        unset($query['createdAt']);
        unset($query['updatedAt']);

        return ['marketplace' => $query];
    }

}