<?php

namespace common\models;

use api\components\HttpException;
use Carbon\Carbon;
use common\base\ActiveRecord;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Provider
 *
 * @package common\models
 *
 * @property string           $id
 * @property string           $name
 * @property string           $type
 * @property string           $authMethod
 * @property string           $authUser
 * @property string           $host
 * @property string           $authUrl
 * @property string           $token
 * @property int              $tokenExpiredIn
 * @property string           $tokenExpiredAt
 * @property string           $requestMethod
 * @property string           $requestBody
 * @property int              $requestTimeout
 * @property string           $responseLanguage
 * @property string           $proxy
 * @property string           $status
 * @property string           $createdAt
 * @property string           $updatedAt
 *
 * @property ProviderConfig[] $configs
 * @property Client           $client
 * @property array            $configGroup
 */
class Provider extends ActiveRecord
{

    const TYPE_TOKOPEDIA = 'tokopedia';
    const TYPE_SHOPEE    = 'shopee';
    const TYPE_BUKALAPAK = 'bukalapak';

    const AUTH_METHOD_FORM   = 'form';
    const AUTH_METHOD_BASIC  = 'basic';
    const AUTH_METHOD_BEARER = 'bearer';
    const AUTH_METHOD_BODY   = 'body';
    const AUTH_METHOD_HEADER = 'header';

    const REQUEST_METHOD_POST = 'post';
    const REQUEST_METHOD_GET  = 'get';

    const REQUEST_BODY_JSON      = 'json';
    const REQUEST_BODY_FORM      = 'form_params';
    const REQUEST_BODY_MULTIPART = 'multipart';

    const RESPONSE_LANGUAGE_JSON = 'json';
    const RESPONSE_LANGUAGE_XML  = 'xml';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const DEFAULT_REQUEST_TIMEOUT = 100;

    public $response;
    public $logs;
    public $_requestMethod;
    public $_query       = [];
    public $_requestBody = [];
    public $_url;
    /** @var Client */
    private $_client;
    private $_headers = [];
    private $_options = ['verify' => false];
    private $_verify  = false;

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
            self::TYPE_TOKOPEDIA => \Yii::t('app', 'Tokopedia'),
            self::TYPE_BUKALAPAK => \Yii::t('app', 'Bukalapak'),
            self::TYPE_SHOPEE    => \Yii::t('app', 'Shopee'),
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

    private function validateToken()
    {
        if (!$this->token && !$this->tokenExpiredAt) {
            $this->getToken();
        } else {
            if (Carbon::now() >= $this->tokenExpiredAt) {
                $this->getToken();
            }
        }
    }

    public function getToken()
    {
        try {
            $client = new Client([
                'timeout' => ArrayHelper::getValue($this, 'requestTimeout', 10),
                'verify'  => $this->_verify,
            ]);

            $authConfig = ArrayHelper::getValue($this->configGroup, 'authorization', []);
            $authMethod = ArrayHelper::getValue($authConfig, 'authMethod');
            if ($authMethod) {
                $authParams = [];
                if ($authMethod === Provider::AUTH_METHOD_BASIC) {
                    $authParams = [
                        'auth' => [
                            ArrayHelper::getValue($authConfig, 'username'), ArrayHelper::getValue($authConfig, 'password')
                        ]
                    ];
                } else if ($authMethod === Provider::AUTH_METHOD_BODY) {
                    $authParams = [
                        'json' => $authConfig
                    ];
                }
                $response = $client->request($this->requestMethod, $this->authUrl, $authParams);
                if ($response->getStatusCode() == 200) {
                    $responseContents = $this->getResponseContents($response);
                    if ($this->type === self::TYPE_TOKOPEDIA) {
                        $this->token          = ArrayHelper::getValue($responseContents, 'access_token');
                        $this->tokenExpiredIn = ArrayHelper::getValue($responseContents, 'expires_in');
                        $this->tokenExpiredAt = Carbon::now()->subMinutes(30)->addSecond($this->tokenExpiredIn)->format('Y-m-d H:i:s');
                        $this->save();
                    } else if ($this->type === self::TYPE_SHOPEE) {

                    }
                }
            }

        } catch (\Exception $e) {
            throw  new HttpException(400, 'Failed to get token');
        }

    }

    private function getResponseContents(ResponseInterface $response, $isXML = FALSE)
    {
        if ($isXML) {
            $responseContent = simplexml_load_string($response->getBody()->getContents());
            return $responseContent->message;
        } else {
            return Json::decode($response->getBody()->getContents());
        }
    }

    public function send()
    {
        try {
            $this->setClient();
            $this->setRequestBody();
            $client   = $this->getClient();
            $response = $client->request($this->_requestMethod, $this->host . $this->_url, $this->getRequestOptions());
//            return Json::decode($response->getBody()->getContents());
            return Json::decode((string)$response->getBody());
        } catch (GuzzleException $e) {
            throw new HttpException(400, \Yii::t('app', 'An error occurred'));
        }
    }

    public function setRequestBody()
    {
        foreach ($this->configs as $config) {
            if ($config->group == ProviderConfig::GROUP_ATTRIBUTE_KEY) {
                $bodyIdentifier = $config->key;
                $keys           = explode('.', $config->value);
            }
        }
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setClient()
    {
        $logger = new \Monolog\Logger('GuzzleLog');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(\Yii::getAlias('@runtime') . '/logs/guzzle.log'), \Monolog\Logger   ::DEBUG);
        $stack = HandlerStack::create();
        $stack->push(Middleware::log(
            $logger, new \GuzzleHttp\MessageFormatter('{req_headers} - {req_body} {res_body}')
        ));

        $this->addAuthorization();
        $this->addHeaders();
        $this->setRequestOptions();
        $this->_client = new Client([
            'base_uri'       => $this->host,
            'timeout'        => ArrayHelper::getValue($this, 'requestTimeout', 10),
            'headers'        => $this->_headers,
            'requestOptions' => $this->_options,
//            'handler'        => $stack,
        ]);
    }

    public function getRequestOptions()
    {
        $requestOptions = [
            'verify' => $this->_verify,
        ];


        if ($this->_query) {
            $requestOptions['query'] = $this->_query;

        }
        if ($this->_requestBody) {
            $requestOptions['json'] = $this->_requestBody;
        }
        if ($this->proxy) {
            $requestOptions['proxy'] = $this->proxy;
        }
        return $requestOptions;

    }


    public function getConfigGroup()
    {
        return ArrayHelper::map($this->configs, 'key', 'value', 'group');
    }

    private function setRequestOptions()
    {
        $this->_query       = isset($this->_query) ? ArrayHelper::merge(ArrayHelper::getValue($this->configGroup, ProviderConfig::GROUP_QUERY, []), $this->_query) : ArrayHelper::getValue($this->configGroup, ProviderConfig::GROUP_QUERY, []);
        $this->_requestBody = isset($this->_requestBody) ? ArrayHelper::merge(ArrayHelper::getValue($this->configGroup, ProviderConfig::GROUP_JSON_BODY, []), $this->_requestBody) : ArrayHelper::getValue($this->configGroup, ProviderConfig::GROUP_JSON_BODY, []);
    }

    private function addAuthorization()
    {
        if ($this->authMethod) {
            if ($this->authMethod == self::AUTH_METHOD_BASIC) {
                $this->_headers['Authorization'] = 'Basic ' . base64_encode($this->authUser . ":" . $this->token);
            } elseif ($this->authMethod == self::AUTH_METHOD_BEARER) {
                $this->_headers['Authorization'] = 'Bearer ' . $this->token;
            } else {
                $this->_headers['Authorization'] = $this->authUser . $this->token;
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

}