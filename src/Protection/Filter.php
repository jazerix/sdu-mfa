<?php

namespace SDU\MFA\Protection;

use SDU\MFA\Azure\User;
use SDU\MFA\Role;

abstract class Filter
{
    /** @var string[] */
    protected $roles = [];

    /**
     * Filter constructor.
     * @param string[] $roles
     */
    public function __construct(...$roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string[]
     */
    public function roles() : array
    {
        return $this->roles;
    }

    public abstract function access(User $user) : bool;
}