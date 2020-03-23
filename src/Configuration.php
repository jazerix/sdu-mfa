<?php

namespace SDU\MFA;

class Configuration
{
    /**
     * @var RoleCollection
     */
    private $roles;

    /**
     * @var string|null
     */
    private $unauthorizedRedirect = null;

    private $clientId;

    private $clientSecret;

    private $tenantId;

    /**
     * Configuration constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $tenantId
     */
    public function __construct(string $clientId, string $clientSecret, string $tenantId)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->roles = new RoleCollection();
    }


    public function addRole(Role $role) : Configuration
    {
        $this->roles->add($role);

        return $this;
    }

    public function setUnauthorizedRedirection(string $uri) : Configuration
    {
        $this->unauthorizedRedirect = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function clientId() : string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function clientSecret() : string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function tenantId() : string
    {
        return $this->tenantId;
    }

    /**
     * @return RoleCollection
     */
    public function roles() : RoleCollection
    {
        return $this->roles;
    }

    /**
     * @return string|null
     */
    public function getUnauthorizedRedirect() : string
    {
        if ($this->unauthorizedRedirect === null)
            return '/unauthorized';
        return $this->unauthorizedRedirect;
    }
}