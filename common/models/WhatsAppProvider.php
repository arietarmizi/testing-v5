<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 3/26/2018
 * Time: 4:48 PM
 */

namespace common\models;


use api\components\HttpException;
use common\base\ActiveRecord;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Lcobucci\JWT\Parser;

/**
 * Class Provider
 *
 * @package common\models
 *
 * @property string           $id
 * @property string           $name
 * @property string           $type
 * @property string           $multipleMethod
 * @property string           $authMethod
 * @property string           $authUser
 * @property string           $authKey
 * @property string           $host
 * @property string           $routeSender
 * @property string           $routeBalance
 * @property string           $requestMethod
 * @property string           $requestBody
 * @property int              $requestTimeout
 * @property string           $responseLanguage
 * @property double           $minimumBalance
 * @property string           $status
 * @property string           $createdAt
 * @property string           $updatedAt
 *
 * @property ProviderConfig[] $configs
 * @property Client           $client
 * @property array            $configGroup
 */
class WhatsAppProvider extends ActiveRecord
{

    const TYPE_WA           = 'wa';
    const TYPE_WA_SECONDARY = 'waSecondary';

    const AUTH_METHOD_FORM   = 'form';
    const AUTH_METHOD_BASIC  = 'basic';
    const AUTH_METHOD_BEARER = 'bearer';
    const AUTH_METHOD_HEADER = 'header';

    const REQUEST_METHOD_POST = 'post';
    const REQUEST_METHOD_GET  = 'get';

    const REQUEST_BODY_JSON      = 'json';
    const REQUEST_BODY_FORM      = 'form_params';
    const REQUEST_BODY_MULTIPART = 'multipart';

    const RESPONSE_LANGUAGE_JSON = 'json';
    const RESPONSE_LANGUAGE_XML  = 'xml';

    const MULTIPLE_METHOD_RECIPIENT = 'multipleRecipient';
    const MULTIPLE_METHOD_MESSAGE   = 'multipleMessage';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const DEFAULT_REQUEST_TIMEOUT = 100;

    const TEMPLATE_TYPE_HSM      = 'hsm';
    const TEMPLATE_TYPE_TEMPLATE = 'template';

    public  $sender;
    public  $title;
    public  $message;
    public  $recipient;
    private $_client;
    private $_headers      = [];
    private $_verify       = false;
    private $_query        = [];
    private $_requestBody  = [];
    private $recipientKey  = false;
    private $recipientsKey = false;
    private $_accessToken;


    public  $template;
    public  $namespace         = 'c5938cd9_9577_4796_b463_c69136b03bac';
    public  $ttl               = 86400;
    private $_templateType     = 'hsm';
    private $_templateLanguage = 'id';
    private $_templateName     = 'purchase_with_credit_card';
    private $_templateParams   = [];
    private $_whatsAppId;


    public static function authMethods()
    {
        return [
            self::AUTH_METHOD_BASIC  => \Yii::t('app', 'HTTP Basic'),
            self::AUTH_METHOD_BEARER => \Yii::t('app', 'HTTP Bearer'),
            self::AUTH_METHOD_FORM   => \Yii::t('app', 'Form Parameter'),
        ];
    }

    public static function requestMethods()
    {
        return [
            self::REQUEST_METHOD_POST => \Yii::t('app', 'POST'),
            self::REQUEST_METHOD_GET  => \Yii::t('app', 'GET'),
        ];
    }

    public static function requestBodies()
    {
        return [
            self::REQUEST_BODY_JSON      => \Yii::t('app', 'JSON'),
            self::REQUEST_BODY_FORM      => \Yii::t('app', 'Form Parameters'),
            self::REQUEST_BODY_MULTIPART => \Yii::t('app', 'Multipart'),
        ];
    }

    public static function responseLanguages()
    {
        return [
            self::RESPONSE_LANGUAGE_XML  => \Yii::t('app', 'XML'),
            self::RESPONSE_LANGUAGE_JSON => \Yii::t('app', 'JSON'),
        ];
    }

    public static function tableName()
    {
        return '{{%provider}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_WA           => \Yii::t('app', 'Whatsapp'),
            self::TYPE_WA_SECONDARY => \Yii::t('app', 'Whatsapp Secondary'),
        ];
    }

    public static function multipleMethods()
    {
        return [
            self::MULTIPLE_METHOD_RECIPIENT => 'Recipient',
            self::MULTIPLE_METHOD_MESSAGE   => 'Message'
        ];
    }

    public function getConfigs()
    {
        return $this->hasMany(ProviderConfig::className(), ['providerId' => 'id']);
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->validateToken();
    }

    public function send()
    {
        $this->validateContact();
        try {
            $this->addAuthorization();
            $this->addHeaders();
            $this->setClient();
            $client   = $this->getClient();
            $response = $client->request($this->requestMethod, $this->routeSender, [
                'json' => $this->getBody()
            ]);
            return [
                [
                    'recipient' => $this->recipient,
                    'code'      => $response->getStatusCode(),
                    'contents'  => $this->getResponseContents($response)
                ]
            ];

        } catch (\Exception $e) {
            $this->getToken();
            throw  new HttpException(400, 'Failed to send messages');
        }
    }

    private function getBody()
    {
        $jsonBody = [];

        $language = ArrayHelper::getValue($this->template, 'language');
        $type     = ArrayHelper::getValue($this->template, 'type');
        $name     = ArrayHelper::getValue($this->template, 'name');
        $params   = ArrayHelper::getValue($this->template, 'params');

        $this->_templateLanguage = isset($language) ? $language : $this->_templateLanguage;
        $this->_templateType     = isset($type) ? $type : $this->_templateType;
        $this->_templateName     = isset($name) ? $name : $this->_templateName;
        $this->_templateParams   = isset($params) ? $params : $this->_templateParams;

        $templateParams = [];
        if ($this->_templateParams) {
            foreach ($this->_templateParams as $param) {
                if ($this->_templateType == self::TEMPLATE_TYPE_HSM) {
                    $templateParams[] = [
                        'default' => $param
                    ];
                } else if ($this->_templateType == self::TEMPLATE_TYPE_TEMPLATE) {
                    $templateParams[] = [
                        'type' => 'text',
                        'text' => $param
                    ];
                }


            }
        }

        if ($this->_templateType == self::TEMPLATE_TYPE_TEMPLATE) {
            $jsonBody = [
                'to'             => $this->_whatsAppId,
                'recipient_type' => 'individual',
                'type'           => $this->_templateType,
                'template'       => [
                    'namespace'  => $this->namespace,
                    'name'       => $this->_templateName,
                    'language'   => [
                        'code'   => $this->_templateLanguage,
                        'policy' => 'deterministic',
                    ],
                    'components' => [[
                        'type'       => 'body',
                        'parameters' => $templateParams
                    ]
                    ]
                ]
            ];
        } else if ($this->_templateType == self::TEMPLATE_TYPE_HSM) {
            $jsonBody = [
                'to'   => $this->_whatsAppId,
//                'ttl'  => $this->ttl,
                'type' => $this->_templateType,
                'hsm'  => [
                    'namespace'          => $this->namespace,
                    'element_name'       => $this->_templateName,
                    'language'           => [
                        'policy' => 'deterministic',
                        'code'   => $this->_templateLanguage
                    ],
                    'localizable_params' => $templateParams

                ]
            ];
        }
        return $jsonBody;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setClient()
    {

//        $logger = new \Monolog\Logger('GuzzleLog');
//        $logger->pushHandler(new \Monolog\Handler\StreamHandler(\Yii::getAlias('@runtime') . '/logs/guzzle.log'), \Monolog\Logger::DEBUG);
//        $stack = HandlerStack::create();
//        $stack->push(Middleware::log(
//            $logger, new \GuzzleHttp\MessageFormatter('{req_headers} - {req_body} {res_body}')
//        ));

        $this->_client = new Client([
            'base_uri' => $this->host,
            'timeout'  => ArrayHelper::getValue($this, 'requestTimeout', 10),
            'headers'  => $this->_headers,
//            'handler'  => $stack,
            'verify'   => $this->_verify,
        ]);
    }


    private function getResponseContents(Response $response)
    {
        return Json::decode($response->getBody());
    }

    public function getConfigGroup()
    {
        return ArrayHelper::map($this->configs, 'key', 'value', 'group');
    }


    private function addAuthorization()
    {
        if ($this->authMethod) {
            if ($this->authMethod == self::AUTH_METHOD_BASIC) {
                $this->_headers['Authorization'] = 'Basic ' . base64_encode($this->authUser . ":" . $this->authKey);
            } elseif ($this->authMethod == self::AUTH_METHOD_BEARER) {
                $this->_headers['Authorization'] = 'Bearer ' . $this->authKey;
            } else {
                $this->_headers['Authorization'] = $this->authUser . $this->authKey;
            }
        }
    }

    private function addHeaders()
    {
        $configHeaders = ArrayHelper::getValue($this->configGroup, ProviderConfig::GROUP_HEADER, []);

        foreach ($configHeaders as $headerKey => $headerValue) {
            $this->_headers[$headerKey] = $headerValue;
        }
    }

    private function validateToken()
    {
        if (!$this->authKey) {
            $this->getToken();

        } else {
            $token       = (new Parser())->parse((string)$this->authKey);
            $expiredDate = $token->getClaim('exp');
            if ($expiredDate < time()) {
                $this->getToken();
            }

        }
    }


    public function getToken()
    {
        try {
            $logger = new \Monolog\Logger('GuzzleLog');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler(\Yii::getAlias('@runtime') . '/logs/guzzle.log'), \Monolog\Logger::DEBUG);
            $stack = HandlerStack::create();
            $stack->push(Middleware::log(
                $logger, new \GuzzleHttp\MessageFormatter('{req_headers} - {req_body} {res_body}')
            ));

            $client = new Client([
                'base_uri' => $this->host,
                'timeout'  => ArrayHelper::getValue($this, 'requestTimeout', 10),
                'handler'  => $stack,
                'verify'   => $this->_verify,
            ]);

            $authConfig = ArrayHelper::getValue($this->configGroup, 'authConfig', []);
            $response   = $client->request($this->requestMethod, 'users/login', [
                'json' => $authConfig
            ]);
            if ($response->getStatusCode() == 200) {
                $this->authKey = ArrayHelper::getValue($this->getResponseContents($response), 'access_token');
                $this->save();
            }
        } catch (\Exception $e) {
            throw  new HttpException(400, 'Failed to get token');
        }

    }

    public function waValidation()
    {
        $this->addAuthorization();
        $this->setClient();
        $client   = $this->getClient();
        $response = $client->request($this->requestMethod, 'contacts', [
            'json' => [
                'contacts' => [$this->recipient],
                'blocking' => 'wait'
            ]
        ]);
        return ArrayHelper::getValue($this->getResponseContents($response), 'contacts');
    }

    public function validateContact()
    {
        $registeredPhoneNumber = RegisteredPhoneNumber::findOne(['phoneNumber' => $this->recipient, 'providerId' => $this->id]);

        if (!$registeredPhoneNumber) {
            $contacts = $this->waValidation();
            if ($contacts) {
                foreach ($contacts as $contact) {
                    $phoneNumber       = ArrayHelper::getValue($contact, 'input');
                    $status            = ArrayHelper::getValue($contact, 'status');
                    $waId              = ArrayHelper::getValue($contact, 'wa_id');
                    $this->_whatsAppId = $waId;
                    if ($status == RegisteredPhoneNumber::STATUS_VALID) {
                        $registeredPhoneNumber              = new RegisteredPhoneNumber();
                        $registeredPhoneNumber->providerId  = $this->id;
                        $registeredPhoneNumber->phoneNumber = $phoneNumber;
                        $registeredPhoneNumber->waId        = $waId;
                        $registeredPhoneNumber->status      = $status;
                        $registeredPhoneNumber->save();
                    } else if ($status == RegisteredPhoneNumber::STATUS_INVALID) {
                        throw  new HttpException(400, 'No.Handphone anda tidak terdaftar di WhatsApp.');
                    } else if ($status == RegisteredPhoneNumber::STATUS_PROCESSING) {
                        throw  new HttpException(400, 'No.Handphone anda sedang proses verfikasi, silahkan coba beberapa saat lagi.');
                    } else {
                        throw  new HttpException(400, 'An error accured!');
                    }
                }
            }
        } else {
            if ($registeredPhoneNumber->isExpired) {
                $contacts = $this->waValidation();
                if ($contacts) {
                    foreach ($contacts as $contact) {
                        $phoneNumber       = ArrayHelper::getValue($contact, 'input');
                        $status            = ArrayHelper::getValue($contact, 'status');
                        $waId              = ArrayHelper::getValue($contact, 'wa_id');
                        $this->_whatsAppId = $waId;
                        if ($status == RegisteredPhoneNumber::STATUS_VALID) {
                            $registeredPhoneNumber->providerId  = $this->id;
                            $registeredPhoneNumber->phoneNumber = $phoneNumber;
                            $registeredPhoneNumber->waId        = $waId;
                            $registeredPhoneNumber->status      = $status;
                            $registeredPhoneNumber->updatedAt   = date('Y-m-d H:i:s');
                            $registeredPhoneNumber->save();
                        } else if ($status == RegisteredPhoneNumber::STATUS_INVALID) {
                            throw  new HttpException(400, 'No.Handphone anda tidak terdaftar di WhatsApp.');
                        } else if ($status == RegisteredPhoneNumber::STATUS_PROCESSING) {
                            throw  new HttpException(400, 'No.Handphone anda sedang proses verfikasi, silahkan coba beberapa saat lagi.');
                        } else {
                            throw  new HttpException(400, 'An error accured!');
                        }
                    }
                }
            } else {
                $this->_whatsAppId = $registeredPhoneNumber->waId;
            }
        }


    }

}