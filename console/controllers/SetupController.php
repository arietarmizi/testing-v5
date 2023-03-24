<?php

namespace console\controllers;

use common\helpers\FolderManager;
use common\models\Admin;
use common\models\Provider;
use common\models\ProviderConfig;
use common\models\SystemApp;
use common\models\SystemConfig;
use common\models\WhatsAppProvider;
use console\setup\Development;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Role;

class SetupController extends Controller
{
    public function actionInit()
    {
        $this->actionSystemApp();
        $this->actionSystemConfig();
        $this->actionAdmin();
        $this->actionProvider();
        $this->actionInitFolder();
    }

    public function actionSystemApp()
    {
        $this->stdout("Prepare init data for development...\n");

        $this->stdout("\nCreating a new SystemApp...\n", Console::FG_BLUE);
        /** @var SystemApp $systemApp */
        $systemApp = Development::createSystemApp();
        $this->stdout($systemApp->name . " system app created!...\n", Console::FG_GREEN);

    }

    public function actionSystemConfig()
    {
        $configs = [SystemConfig::FIREBASE_TOKEN => 'AIzaSyBGmBwQgFXFdpptsDRAYU6Dlub0JopsuLo'];

        $this->stdout("Prepare init data for development...\n");

        foreach ($configs as $key => $value) {
            /** @var SystemConfig $systemConfig */
            $systemConfig = Development::createSystemConfig($key, $value);
            $this->stdout($systemConfig->key . " config created!...\n", Console::FG_GREEN);
        }
    }

    public function actionAdmin()
    {
        $this->stdout("\nCreating roles...\n", Console::FG_BLUE);
        /** @var Role[] $admins */
        $roles = Development::createRole();

        foreach ($roles as $role) {
            $this->stdout($role->name . " role created!...\n", Console::FG_GREEN);
        }

        $this->stdout("\nCreating admins...\n", Console::FG_BLUE);
        /** @var Admin[] $admins */
        $admins = Development::createAdmin();

        foreach ($admins as $admin) {
            $this->stdout($admin->name . " admin created!...\n", Console::FG_GREEN);
        }
    }

    public function actionProvider()
    {
        $this->stdout("\nCreating providers...\n", Console::FG_BLUE);

        /** @var Provider $provider */
        $provider                   = new Provider();
        $provider->name             = 'Tokopedia';
        $provider->type             = Provider::TYPE_TOKOPEDIA;
        $provider->host             = 'https://fs.tokopedia.net/';
        $provider->authUrl          = 'https://accounts.tokopedia.com/token?grant_type=client_credentials';
        $provider->proxy            = '103.30.246.27:3128';
        $provider->authMethod       = Provider::AUTH_METHOD_BEARER;
        $provider->requestMethod    = Provider::REQUEST_METHOD_POST;
        $provider->responseLanguage = Provider::RESPONSE_LANGUAGE_JSON;

        $provider->save();

        $providerConfigs = [
            ProviderConfig::GROUP_AUTHORIZATION => [
                ProviderConfig::ATTRIBUTE_KEY_AUTH_METHOD => Provider::AUTH_METHOD_BASIC,
                'username'                                => '69afbc90fbb3412383b7ef274a0d8993',
                'password'                                => '502c41a706104fe39ab31f8823a15cf4',
            ],
        ];

        foreach ($providerConfigs as $configGroup => $configData) {
            foreach ($configData as $configKey => $configValue) {
                $providerConfig             = new ProviderConfig();
                $providerConfig->providerId = $provider->id;
                $providerConfig->group      = $configGroup;
                $providerConfig->key        = $configKey;
                $providerConfig->value      = $configValue;

                $providerConfig->save();
            }
        }

        $this->stdout($provider->name . " provider created!...\n", Console::FG_GREEN);
    }

    public function actionInitFolder()
    {
        $this->stdout("Prepare init product image folder...\n");

        $this->stdout("\nCreating required folder...\n", Console::FG_BLUE);

        $ProductImageFolder = FolderManager::makeDirectory('@api', 'uploads' . DIRECTORY_SEPARATOR . 'product');

        $this->stdout("Folder product image created successfully!...\n", Console::FG_GREEN);
    }
}
