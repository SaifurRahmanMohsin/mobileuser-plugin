<?php namespace Mohsin\User\Classes;

use ApplicationException;

/**
 * Represents the generic login provider.
 * All other login providers must be derived from this class
 */
abstract class ProviderBase
{
    /**
     * Returns information about the login provider
     * Must return array:
     *
     * [
     *      'name'        => 'AccountKit',
     *      'description' => 'Facebook AccountKit Mobile Login provider.'
     * ]
     *
     * @return array
     */
    abstract protected function providerDetails();

    /**
     * Sign in the user
     */
    abstract protected function signin();

    /**
     * Register the user
     */
    abstract protected function register();
}
