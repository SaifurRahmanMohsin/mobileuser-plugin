<?php namespace Mohsin\User\Providers;

use Mail;
use Lang;
use File;
use Event;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use Mohsin\User\Models\Settings;
use October\Rain\Auth\AuthException;
use Mohsin\User\Classes\ProviderBase;
use October\Rain\Database\ModelException;

/*
 * Default Login Provider that works with RainLab.User
 */
class DefaultProvider extends ProviderBase
{
    /**
     * {@inheritDoc}
     */
    public function providerDetails()
    {
        return [
            'name'        => 'Default Provider',
            'description' => 'The default login provider that works with RainLab\'s User Plugin'
        ];
    }

    /**
     * Returns a user object, after signing in
     */
    public function signin()
    {
        /*
         * Validate input
         */
        $data = post();
        $rules = [];

        // $rules['package'] = 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/|exists:mohsin_mobile_variants,package';
        $rules['login'] = $this->loginAttribute() == Settings::LOGIN_USERNAME
            ? 'required|between:2,255'
            : 'required|email|between:6,255';
        $rules['password'] = 'required|between:4,255';

        if (!array_key_exists('login', $data)) {
            $data['login'] = post('username', post('email'));
        }

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            return response()->json($validation->messages()->first(), 400);
        }

        /*
         * Authenticate user
         */
        $credentials = [
            'login'    => array_get($data, 'login'),
            'password' => array_get($data, 'password')
        ];

        Event::fire('mohsin.user.beforeAuthenticate', [$this, $credentials]);

        try {
            $user = $this->authManager->authenticate($credentials, true)->load('avatar');
            $userArray = $user->toArray();
            $userArray['avatar'] = $user->avatar ? File::localToPublic($user->avatar->getLocalPath()) : null;
            Event::fire('mohsin.user.afterAuthenticate', [$this, $user]);
            return response()->json($userArray, 200);
        } catch (AuthException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /**
     * Returns a user object, after registration
     */
    public function register()
    {
        try {
            if (!Settings::get('allow_registration', true)) {
                return response()->json('registration-disabled', 400);
            }

            /*
             * Validate input
             */
            $data = post();

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email'       => 'required|email|between:6,255',
                'password'    => 'required|between:4,255'
            ];

            // $rules['instance_id'] = 'required|max:16|string|exists:mohsin_mobile_installs,instance_id';

            // $rules['package'] = 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/|exists:mohsin_mobile_variants|registration_enabled';

            if ($this->loginAttribute() == Settings::LOGIN_USERNAME) {
                $rules['username'] = 'required|between:2,255';
            }

            // Validator::extend('registration_enabled', function ($attribute, $value, $parameters, $validator) {
            //     if ($variant = Variant::where('package', '=', $value) -> first()) {
            //         if ($variant -> disable_registration) {
            //             throw new ValidationException(['package' => trans('mohsin.user::lang.variants.registration_disabled')]);
            //         }
            //         return true;
            //     }
            //     return false;
            // });

            $validation = Validator::make($data, $rules);

            if ($validation->fails()) {
                return response()->json($validation->messages()->first(), 400);
            }

            /*
             * Register user
             */
            $requireActivation = Settings::get('require_activation', true);
            $automaticActivation = Settings::get('activate_mode') == Settings::ACTIVATE_AUTO;
            $userActivation = Settings::get('activate_mode') == Settings::ACTIVATE_USER;
            $user = $this->authManager->register($data, $automaticActivation);

            /*
             * Activation is by the user, send the email
             */
            if ($userActivation) {
                $this->sendActivationEmail($user);
            }

            /*
             * Return the created record on successful registration
             */
             return response()->json($user, 200);
        }
        catch (ModelException $ex) {
            return response()->json($ex->getMessage(), 400);
        }
        catch (ValidationException $ex) {
            return response()->json($ex->getMessage(), 400);
        }
        catch (Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /*
     * Used internally, ripped off from RainLab.User
     *
     * Returns the login model attribute.
     */
    public function loginAttribute()
    {
        return Settings::get('login_attribute', Settings::LOGIN_EMAIL);
    }

    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $page = Settings::get('activation_page', '404');
        $link = Page::url($page) . '/' . $code;

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }
}
