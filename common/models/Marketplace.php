<?php


namespace common\models;


use Carbon\Carbon;
use common\base\ActiveRecord;

/**
 * Class Marketplace
 * @package common\models
 *
 * @property $id
 * @property $code
 * @property $merchantId
 * @property $marketplaceName
 * @property $description
 * @property $status
 * @property $createdAt
 * @property $updatedAt
 */
class Marketplace extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED  = 'deleted';

    const TOKOPEDIA = 'tokopedia';
    const BUKALAPAK = 'bukalapak';
    const SHOPEE    = 'shopee';

    public static function marketplaces()
    {
        return [
            self::TOKOPEDIA => \Yii::t('app', 'Tokopedia'),
            self::BUKALAPAK => \Yii::t('app', 'Bukalapak'),
            self::SHOPEE    => \Yii::t('app', 'Shopee'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive'),
        ];
    }

    public static function tableName()
    {
        return '{{%marketplace}}';
    }

    public function generateCode()
    {
        $code = Marketplace::find()
            ->where([
                'between',
                'createdAt',
                Carbon::now()->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::now()->endOfDay()->format('Y-m-d H:i:s')
            ])->count();

        $this->code = 'MKP' . date('Ymd') . substr((100000 + ($code + 1)), 1);
    }

//    public function getMasterStatus()
//    {
//        return $this->hasOne(MasterStatus::class, ['id' => 'marketplaceId']);
//    }
}