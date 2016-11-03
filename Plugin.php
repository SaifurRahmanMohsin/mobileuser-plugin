<?php namespace Mohsin\User;

use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
use Mohsin\User\Classes\ProviderManager;
use RainLab\User\Models\User as UserModel;
use Mohsin\Mobile\Models\Install as InstallModel;
use Mohsin\User\Models\Settings as SettingsModel;
use RainLab\User\Controllers\Users as UsersController;
use Mohsin\Mobile\Controllers\Installs as InstallsController;

/**
 * User Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['Mohsin.Mobile', 'RainLab.User'];

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
            'author'      => 'Mohsin',
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
        UserModel::extend(function($model){
            $model -> hasMany['mobileuser_installs'] = ['Mohsin\Mobile\Models\Install'];
        });

        InstallModel::extend(function($model){
            $model -> belongsTo['user'] = ['RainLab\User\Models\User'];
        });

        InstallsController::extendListColumns(function($list, $model){
            if (!$model instanceof InstallModel)
                return;

            $list->addColumns([
                'user' => [
                    'label' => 'User',
                    'relation' => 'user',
                    'valueFrom' => 'id',
                    'default' => Lang::get('mohsin.user::lang.installs.unregistered')
                ]
            ]);

        });

        UsersController::extend(function($controller){
            $controller->addCss('/plugins/mohsin/user/assets/css/custom.css');

            if(!isset($controller->implement['Backend.Behaviors.RelationController']))
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            $controller->relationConfig  =  '$/mohsin/user/models/relation.yaml';
        });

        UsersController::extendFormFields(function($form, $model, $context){
            if(!$model instanceof UserModel)
                return;

            if(!$model->exists)
              return;

            $form->addTabFields([
                'mobileuser_installs' => [
                    'label' => 'Mobile User Installs',
                    'tab' => 'Mobile',
                    'type' => 'partial',
                    'path' => '$/mohsin/user/assets/partials/_field_mobileuser_installs.htm',
                  ],

              ]);
        });

        Event::listen('backend.form.extendFields', function ($form) {
           if (!$form->model instanceof SettingsModel)
                return;

           $value = $form->getField('provider')->value;

           if(!is_null($value))
            {
                $provider = ProviderManager::instance()->listProviders(true)->get($value)->object;
                if(method_exists($provider,'extraFields'))
                {
                   $extras = $provider->extraFields();
                   $form->addTabFields($extras);
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
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'user' => [
                'label'       => 'User',
                'url'         => Backend::url('mohsin/user/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['mohsin.user.*'],
                'order'       => 500,
            ],
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
     * Registers any mobile login providers implemented in this plugin.
     * The providers must be returned in the following format:
     * ['className1' => 'alias'],
     * ['className2' => 'anotherAlias']
     */
    public function registerMobileLoginProviders()
    {
        return [
            'Mohsin\User\Providers\DefaultProvider' => 'default'
        ];
    }

}
