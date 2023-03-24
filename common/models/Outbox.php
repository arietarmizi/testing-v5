<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 3/26/2018
 * Time: 4:49 PM
 */

namespace common\models;


use common\base\ActiveRecord;

/**
 * Class Outbox
 *
 * @package common\models
 *
 * @property string  $id
 * @property string  $providerId
 * @property string  $type
 * @property string  $sender
 * @property string  $recipient
 * @property string  $title
 * @property string  $message
 * @property string  $response
 * @property integer $responseCode
 * @property string  $responseMessage
 * @property string  $responseId
 * @property string  $responseBalance
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 */
class Outbox extends ActiveRecord
{

    const STATUS_SENT    = 'sent';
    const STATUS_FAILED  = 'failed';
    const STATUS_PENDING = 'pending';
    const STATUS_UNKNOWN = 'unknown';

    const TYPE_VERIFICATION_CODE = 'verificationCode';

    public static function tableName()
    {
        return '{{%outbox}}';
    }
}