<?php

namespace common\models;

use common\base\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class District
 *
 * @package common\models
 * @property string $id
 * @property string $cityId
 * @property string $name
 * @property string $description
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class District extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%district}}';
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

    public static function districts2()
    {
        $districts = self::find(['status' => self::STATUS_ACTIVE])
            ->joinWith('city')
            ->joinWith('city.province')
            ->all();

        return ArrayHelper::map($districts, 'id', 'name', 'city.name');
    }

    public static function districts()
    {
        $districts = self::findAll(['status' => self::STATUS_ACTIVE]);

        return ArrayHelper::map($districts, 'id', 'name');
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'provinceId'  => \Yii::t('app', 'Province'),
            'cityId'      => \Yii::t('app', 'City'),
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
            'provinceId'  => \Yii::t('app', 'Provinsi'),
            'cityId'      => \Yii::t('app', 'Kabupaten/Kota'),
            'name'        => \Yii::t('app', 'Nama'),
            'description' => \Yii::t('app', 'Deskripsi'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Tanggal Dibuat'),
            'updatedAt'   => \Yii::t('app', 'Tanggal Diperbarui'),
        ];
    }

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'cityId']);
    }
}
