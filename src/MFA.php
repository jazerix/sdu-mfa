<?php

namespace SDU\MFA;

use SDU\MFA\Azure\User;
use SDU\MFA\Exceptions\AlreadyInitializedException;
use SDU\MFA\Exceptions\NotInitializedException;
use SDU\MFA\Protection\Filter;
use SDU\MFA\Protection\Protection;

class MFA
{
    /**
     * @var MFA The MFA Instance
     */
    private static $instance = null;
    /**
     * @var Configuration
     */
    private static $configuration = null;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    private function __construct(Configuration $configuration)
    {
        self::$configuration = $configuration;
    }

    /**
     * @var Protection
     */
    private static $protection = null;

    /**
     * Should be called upon as part of your application's boot-up.
     * @param Configuration $configuration
     * @return Configuration
     * @throws AlreadyInitializedException
     */
    public static function initialize(Configuration $configuration) : Configuration
    {
        if (self::$instance !== null)
            throw new AlreadyInitializedException("MFA Has already been initialized");

        self::$configuration = $configuration;
        self::$instance = new MFA(self::$configuration);

        if (session_status() == PHP_SESSION_NONE)
            session_start();

        self::$instance->authenticationService = new AuthenticationService(self::configuration());
        self::$instance->authenticationService->handleCallback();

        return self::$configuration;
    }

    /**
     * @param Filter $filter
     * @return void
     * @throws NotInitializedException
     */
    public static function protect(Filter $filter)
    {
        if (self::isGuest())
            self::instance()->authenticate();
        if (self::user()->roles()->empty() && empty($filter->roles()))
            self::instance()->unauthorizedRedirect();
        if ( ! $filter->access(self::user()))
            self::instance()->unauthorizedRedirect();
    }

    /**
     * @param Protection $protection
     * @return void
     * @throws NotInitializedException
     */
    public static function protectAll(Protection $protection)
    {
        $filter = $protection->matchesPath();
        if ($filter === null)
            return;

        if (self::isGuest())
            self::instance()->authenticate();
        if ( ! $filter->access(self::user()))
            self::instance()->unauthorizedRedirect();
    }

    /**
     * @return MFA
     * @throws NotInitializedException
     */
    private static function instance()
    {
        if (self::$instance === null)
            throw new NotInitializedException("MFA Hasn't been initialized yet!");
        return self::$instance;
    }

    /**
     * @return null|User
     */
    public static function user()
    {
        if (array_key_exists('mfa_user', $_SESSION))
            return $_SESSION['mfa_user'];
        return null;
    }

    public static function isGuest() : bool
    {
        return ! self::isAuthenticated();
    }

    public static function isAuthenticated() : bool
    {
        return self::user() !== null;
    }

    public static function configuration()
    {
        return self::$configuration;
    }

    /**
     * @throws NotInitializedException
     */
    private function authenticate()
    {
        self::instance()->authenticationService->authenticate();
    }

    private function unauthorizedRedirect()
    {
        header("Location: " . self::$configuration->getUnauthorizedRedirect());
        exit();
    }

    public static function logout($uri = "/")
    {
        if (array_key_exists('mfa_user', $_SESSION))
            unset($_SESSION['mfa_user']);
        header("Location: $uri");
        exit;
    }
}