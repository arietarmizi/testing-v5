<?php

namespace api\forms\category;

use api\components\BaseForm;
use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductSubCategory;
use common\models\Provider;
use yii\helpers\ArrayHelper;

class ScrapFrom extends BaseForm
{
    public $fsId;
    public $id;
    public $name;
    public $child;

    private $_response;

    public function rules()
    {
        return [
            [['fsId'], 'required'],
            [['id', 'fsId'], 'number'],
            ['name', 'string']
        ];
    }

    public function submit()
    {
        $provider                 = \Yii::$app->tokopediaProvider;
        $provider->_url           = 'inventory/v1/fs/' . $this->fsId . '/product/category';
        $provider->_requestMethod = Provider::REQUEST_METHOD_GET;

        $this->_response = $provider->send();


        $responseCategory = $this->_response['data'];

        $this->loopChild($responseCategory['categories']);
        return true;
    }

	/**
	 * @param array $arrCategories
	 * @param string $parent
	 */
    public function loopChild($arrCategories,$parent = null){
//			print_r($arrCategory);

			foreach ($arrCategories as $rowCategory) {

				$category = new ProductCategory();

				$category->id = $rowCategory['id'];
				$category->name = $rowCategory['name'];
				$category->parentId = $parent;
				$category->save(false);

				if (isset($rowCategory['child']) && is_array($rowCategory['child'])) {
					$this->loopChild($rowCategory['child'], $rowCategory['id']);
				}
			}
		}

    public function response()
    {
        return $this->_response;
    }
}

