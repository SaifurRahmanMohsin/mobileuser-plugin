<?php namespace Mohsin\User;

use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use Mohsin\User\Classes\ProviderManager;
use RainLab\User\Models\User as UserModel;
use Mohsin\User\Models\Settings as SettingsModel;
use RainLab\User\Controllers\Users as UsersController;

/**
 * User Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['Mohsin.Rest', 'RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'mohsin.user::lang.plugin.name',
            'description' => 'mohsin.user::lang.plugin.description',
            'author'      => 'Saifur Rahman Mohsin',
            'icon'        => 'icon-user'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        if (PluginManager::instance()->exists('Mohsin.Install')) {
            UserModel::extend(function ($model) {
                $model->hasMany['mobileuser_installs'] = ['Mohsin\Mobile\Models\Install'];
            });

            UsersController::extend(function ($controller) {
                $controller->addCss('/plugins/mohsin/user/assets/css/custom.css');

                if (!$controller->isClassExtendedWith('Backend.Behaviors.RelationController')) {
                    $controller->implement[] = 'Backend.Behaviors.RelationController';
                }

                if (!isset($controller->relationConfig)) {
                    $controller->addDynamicProperty('relationConfig');
                }

                $controller->relationConfig = $controller->mergeConfig(
                    $controller->relationConfig,
                    '$/mohsin/user/models/relation.yaml'
                );
            });

            UsersController::extendFormFields(function ($form, $model, $context) {
                if (!$model instanceof UserModel) {
                    return;
                }
                if (!$model->exists) {
                    return;
                }
                $form->addTabFields([
                    'mobileuser_installs' => [
                        'label' => 'mohsin.user::lang.users.mobileuser_installs_label',
                        'tab' => 'Mobile',
                        'type' => 'partial',
                        'path' => '$/mohsin/user/assets/partials/_field_mobileuser_installs.htm',
                      ],
                  ]);
            });
        }

        Event::listen('backend.form.extendFields', function ($form) {
            if (!$form->model instanceof SettingsModel) {
                return;
            }
            $providers = ProviderManager::instance()->listProviderObjects();
            foreach ($providers as $provider) {
                $config = $provider -> getFieldConfig();
                if (!is_null($config)) {
                    $form->addFields($config);
                }
            }
        });
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'mohsin.user.access_users' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_users'],
            'mohsin.user.access_settings' => ['tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_settings']
        ];
    }

    /**
     * Registers settings controller for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'mohsin.user::lang.settings.name',
                'description' => 'mohsin.user::lang.settings.description',
                'category'    => 'Mobile',
                'icon'        => 'icon-user-plus',
                'class'       => 'Mohsin\User\Models\Settings',
                'order'       => 502,
                'permissions' => ['mohsin.user.access_settings'],
            ]
        ];
    }

    /**
     * Registers API nodes exposed by this plugin.
     *
     * @return array
     */
    public function registerNodes()
    {
        return [
            'account/signin' => [
                'controller' => 'Mohsin\User\Http\Account@signin',
                'action'     => 'store'
            ],
            'account/register' => [
                'controller' => 'Mohsin\User\Http\Account@register',
                'action'     => 'store'
            ]
        ];
    }

    /**
     * Registers any mobile login providers implemented in this plugin.
     * The providers must be returned in the following format:
     * ['className1' => 'alias'],
     * ['className2' => 'anotherAlias']
     */
    public function registerMobileLoginProviders()
    {
        // Note that the DefaultProvider does not need Provider suffix
        //but it's there due to PHP class naming restriction to use the reserved Default keyword.
        return [
            'Mohsin\User\Providers\DefaultProvider' => 'default'
        ];
    }
}
