<?php namespace Mohsin\User\Models;

use Model;
use Cms\Classes\Page;
use Mohsin\User\Classes\ProviderManager;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'mobile_user_settings';

    public $settingsFields = 'fields.yaml';

    const LOGIN_EMAIL = 'email';
    const LOGIN_USERNAME = 'username';

    public function getProviderOptions()
    {
        $values = [];
        $providers = ProviderManager::instance()->listProviderObjects();
        foreach ($providers as $key => $value) {
            $values[$key] = $value->providerDetails()['name'];
        }
        return $values;
    }

    public function getAuthManagerOptions()
    {
        return ProviderManager::instance()->getAvailableAuthManagers()->lists('name', 'id');
    }

    public function getActivationPageOptions($keyValue = null)
    {
        return Page::sortBy('baseFileName')->lists('title', 'baseFileName');
    }

    public function getLoginAttributeOptions()
    {
        return [
            self::LOGIN_EMAIL => ['rainlab.user::lang.login.attribute_email'],
            self::LOGIN_USERNAME => ['rainlab.user::lang.login.attribute_username']
        ];
    }
}
