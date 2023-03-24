<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 3/26/2018
 * Time: 4:49 PM
 */

namespace common\models;


use Carbon\Carbon;
use common\base\ActiveRecord;

/**
 * Class Outbox
 *
 * @package common\models
 *
 * @property string $id
 * @property string $providerId
 * @property string $phoneNumber
 * @property string $waId
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class RegisteredPhoneNumber extends ActiveRecord
{

    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FAILED = 'failed';
    const EXPIRED_VALIDATION = 6;


    public static function tableName()
    {
        return '{{%registered_phone_number}}';
    }

    public function getIsExpired()
    {
        $dateApproved = Carbon::parse($this->updatedAt);
        $dateNow      = Carbon::now();
        $days         = $dateNow->diffInDays($dateApproved, true);
        if ($days >= self::EXPIRED_VALIDATION) {
            return true;
        }
        return false;
    }

}