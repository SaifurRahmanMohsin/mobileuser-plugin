<?php return [
    'plugin' => [
        'name' => 'Mobile User',
        'description' => 'Mobile front-end user management.',
        'access_users' => 'Manage Users',
        'access_settings' => 'Manage User Settings',
    ],
    'settings' => [
        'name' => 'Login settings',
        'description' => 'Manage mobile login configurations.',
        'provider_label' => 'Provider',
        'provider_comment' => 'Choose the login provider to use for your mobile application.',
        'auth_manager_label' => 'Auth Manager',
        'auth_manager_comment' => 'Choose the auth manager provider to use for login.',
        'login_attribute' => 'Login attribute',
        'login_attribute_comment' => 'Select what primary user detail should be used for signing in.',
        'allow_registration' => 'Allow user registration',
        'allow_registration_comment' => 'If this is disabled users can only be created by administrators.',
        'require_activation' => 'Sign in requires activation',
        'require_activation_comment' => 'Users must have an activated account to sign in.',
        'activation_page_label' => 'Activation Page Location',
        'activation_page_comment' => 'Select the page where you have added the Account component for user activation.',
    ],
    'installs' => [
        'unregistered' => 'Unregistered'
    ],
    'variants' => [
        'allow_registration_label' => 'Disable Registration?',
        'allow_registration_comment' => 'When checked, user registration for this variant through the API is disabled.',
        'registration_disabled' => 'Registration is disabled for this package.'
    ],
    'users' => [
        'mobileuser_installs_label' => 'Mobile User Installs'
    ]
];
