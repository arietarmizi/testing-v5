<?php

namespace common\models;

use common\base\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Province
 *
 * @package common\models
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class Province extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%province}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function statusesID()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Aktif'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Tidak aktif'),
        ];
    }

    public static function provinces()
    {
        $provinces = self::findAll(['status' => self::STATUS_ACTIVE]);

        return ArrayHelper::map($provinces, 'id', 'name');
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'code'        => \Yii::t('app', 'Code'),
            'name'        => \Yii::t('app', 'Name'),
            'description' => \Yii::t('app', 'Description'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At'),
        ];
    }

    public function attributeLabelsID()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'code'        => \Yii::t('app', 'Kode'),
            'name'        => \Yii::t('app', 'Nama'),
            'description' => \Yii::t('app', 'Deskripsi'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Tanggal Dibuat'),
            'updatedAt'   => \Yii::t('app', 'Tanggal Diperbarui'),
        ];
    }
}
