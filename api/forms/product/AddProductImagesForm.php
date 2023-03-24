<?php


namespace api\forms\product;


use api\components\BaseForm;
use api\components\HttpException;
use common\models\ProductImages;
use nadzif\file\models\File;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use nadzif\file\FileManager;

class AddProductImagesForm extends BaseForm
{
    public $productVariantId;
    public $type;
    public $imageFiles;
    public $isPrimary;

    private $allowedExtension = ['jpg', 'jpeg', 'png'];

    /** @var File */
    private $_file;

    public function rules()
    {
        return [
            [['productVariantId'], 'required'],
            ['type', 'string'],
            [['imageFiles'], 'file', 'mimeTypes' => ['image/*'], 'extensions' => $this->allowedExtension],
            ['productVariantId', 'string'],
            ['isPrimary', 'boolean']
        ];
    }

    public function submit()
    {
        $db          = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $success     = true;
        $fileManager = \Yii::$app->fileManager;
        $folder      = 'product/' . ArrayHelper::getValue(ProductImages::imageFolders(), 'image');
        if ($this->validate()) {
            $fileInstances = UploadedFile::getInstancesByName('imageFiles');
            if ($fileInstances) {
                foreach ($fileInstances as $fileInstance) {
                    $this->_file             = $fileManager->upload($fileInstance, $folder);
                    $productImage            = new ProductImages();
                    $productImage->productVariantId = $this->productVariantId;
                    $productImage->fileId    = $this->_file->id;
                    $productImage->isPrimary = false;
                    $productImage->status    = ProductImages::STATUS_ACTIVE;
                    $success                 &= $productImage->save();
                }
            } else {
                throw new HttpException(400, \Yii::t('app', 'Please upload image'));
            }
        }
        if ($success) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $success;
    }

    public function response()
    {
        return [];
    }
}