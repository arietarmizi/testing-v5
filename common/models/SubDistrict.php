<?php

namespace common\models;

use common\base\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class SubDistrict
 *
 * @package common\models
 * @property string $id
 * @property string $districtId
 * @property string $name
 * @property string $description
 * @property string $postalCode
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class SubDistrict extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%sub_district}}';
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

    public static function subDistricts()
    {
        $subDistricts = self::findAll(['status' => self::STATUS_ACTIVE]);

        return ArrayHelper::map($subDistricts, 'id', 'name');
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'provinceId'  => \Yii::t('app', 'Province'),
            'cityId'      => \Yii::t('app', 'City'),
            'districtId'  => \Yii::t('app', 'District'),
            'name'        => \Yii::t('app', 'Name'),
            'description' => \Yii::t('app', 'Description'),
            'postalCode'  => \Yii::t('app', 'Postal Code'),
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
            'districtId'  => \Yii::t('app', 'Kecamatan'),
            'name'        => \Yii::t('app', 'Nama'),
            'description' => \Yii::t('app', 'Deskripsi'),
            'postalCode'  => \Yii::t('app', 'Kode Pos'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Tanggal Dibuat'),
            'updatedAt'   => \Yii::t('app', 'Tanggal Diperbarui'),
        ];
    }

    public function getDistrict()
    {
        return $this->hasOne(District::class, ['id' => 'districtId']);
    }

    /*
    public static function subDistricts($districtId = -1)
    {
        $subDistricts = self::findAll(['status' => self::STATUS_ACTIVE, 'districtId' => $districtId]);

        return ArrayHelper::map($subDistricts, 'id', 'name');
    }
    */

    public function textAdministrativeLocation()
    {
        $stringLocation = '';

        $stringLocation = $this['name'];

        $district = $this['district'];
        if ($district) {
            $stringLocation .= ', ' . $district['name'];

            $city = $district['city'];
            if ($city) {
                $stringLocation .= ', ' . $city['name'];

                $province = $city['province'];
                if ($province) {
                    $stringLocation .= ', ' . $province['name'];
                }
            }
        }

        return $stringLocation;
    }
}
