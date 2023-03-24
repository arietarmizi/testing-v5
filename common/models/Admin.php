<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 8/11/2018
 * Time: 5:12 AM
 */

namespace common\models;


use common\base\ActiveRecord;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Class Admin
 *
 * @package common\models
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $phoneNumber
 * @property string $passwordHash
 * @property string $passwordResetToken
 * @property string $authKey
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class Admin extends ActiveRecord implements IdentityInterface
{

    const ROLE_SUPERUSER     = 'Superuser';
    const ROLE_ADMINISTRATOR = 'Administrator';
    const ROLE_SUPERVISOR    = 'Supervisor';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }
    public function attributeLabels()
    {
        return [
            'id'                    => \Yii::t('app', 'Id User'),
            'name'                  => \Yii::t('app', 'Name'),
            'email'                 => \Yii::t('app', 'Email'),
            'phoneNumber'           => \Yii::t('app', 'Phone Number '),
            'passwordHash'          => \Yii::t('app', 'Password Hash'),
            'passwordResetToken'    => \Yii::t('app', 'Password Reset Token'),
            'authKey'               => \Yii::t('app', 'Auth Key'),
            'status'                => \Yii::t('app', 'status'),
            'createdAt'             => \Yii::t('app', 'Created At'),
            'updatedAt'             => \Yii::t('app', 'Updated At')
        ];
    }
    /**
     * @param int|string $id
     *
     * @return Admin|null|IdentityInterface
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    /**
     * @param mixed $token
     * @param null  $type
     *
     * @return void|IdentityInterface
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * @return string|void
     * @throws NotSupportedException
     */
    public function getAuthKey()
    {
        throw new NotSupportedException();
    }

    /**
     * @param string $authKey
     *
     * @return bool|void
     * @throws NotSupportedException
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException();
    }

    /**
     * @return int|mixed|string
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }
    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password){
        $this->passwordHash = \Yii::$app->security->generatePasswordHash($password);
    }
}