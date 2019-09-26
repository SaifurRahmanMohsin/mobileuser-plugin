Mobile Front-end user management for October CMS.

** Update: This plugin now works independent of the Mobile plugin. **

# About Plugin #

This plugin is similar to the RainLab.User plugin except it’s built to work mainly with mobile front-ends. It exposes RESTful API nodes that enable interaction with the backend thereby allowing users to sign up and login as front-end users.

Tutorial and demonstration of the plugin [here](//www.youtube.com/embed/IkFzSzjoXJ0).

#### Features ####
* Completely extensible.

#### Coming Soon ####
* Compatible with the multiple mobile apps feature of the mobile plugin.
* Dashboard widget to provide user signup and register analytics.
* Forgot password and signing out nodes (Currently, you can do it through website only).
* Multiple Auth Schemes for better security.

### Requirements

* [Mohsin.RESTful plugin](http://octobercms.com/plugin/mohsin-rest).
* [Rainlab.User plugin](http://octobercms.com/plugin/rainlab-user).

Most of the [RainLab User Documentation](https://octobercms.com/plugin/rainlab-user#documentation) applies to this as well, since it’s extended over that.

### Client-side Integration ###

On the mobile app, you will have to make a call to the backend using the REST API that has been provided. Right now, the API works like this:

#### POST /account/signin ####

**Resource URL:** [/api/v1/account/signin](/api/v1/account/signin) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
instance_id  | A unique ID, such as device ID or an ID generated using Google’s Instance ID API. Eg. 573b61d82b4e46e3
package  | The package name of the application. Must match the name specified in the variants. Eg. com.acme.myapp
email (or) username* | The login attribute for the user attempting to sign in.
password* | The password for the user attempting to sign in.

#### POST /account/register ####

**Resource URL:** [/api/v1/account/register](/api/v1/account/register) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
name | The name of the user attempting registration.
email* | The email for the user attempting registration.
username | The username for the user attempting registration.
password* | The password for the user attempting registration.

All * (asterisk) fields are mandatory.