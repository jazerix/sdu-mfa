<?php

namespace SDU\MFA;

class RoleCollection
{
    /**
     * @var Role[]
     */
    private $roles = [];

    public function add(Role $role)
    {
        $this->roles[] = $role;
    }

    public function combinedGroupIds()
    {
        $groups = [];
        foreach ($this->roles as $role)
        {
            $groups = array_merge($groups, $role->groupIds());
        }
        return array_unique($groups);
    }

    /**
     * @param array $memberShips
     * @return Role[]
     */
    public function matchingRoles(array $memberShips)
    {
        $roles = [];
        foreach ($this->roles as $role)
        {
            if (empty(array_diff($role->groupIds(), $memberShips)))
                $roles[] = $role;
        }
        return $roles;
    }

    public function empty()
    {
        return count($this->roles) == 0;
    }

    public function names()
    {
        $names = [];
        foreach ($this->roles as $role)
        {
            $names[] = $role->name();
        }

        return array_unique($names);
    }
}