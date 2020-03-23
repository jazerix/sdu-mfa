<?php

namespace SDU\MFA;

use SDU\MFA\Exceptions\InvalidGuidException;

class Role
{
    /**
     * @var string Specifies the id of the group
     */
    private $name;

    /**
     * @var string[] Name of AD groups the user has to belong to in order to be considered the role.
     */
    private $groupIds;

    /**
     * Role constructor.
     * @param string $name Id of the Role
     * @param string[] $groupIds Groups that make a user become that role
     * @throws InvalidGuidException
     */
    public function __construct(string $name, ... $groupIds)
    {
        $this->name = trim($name);
        foreach($groupIds as $groupId)
        {
            if (preg_match("/^({)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)})$/i", $groupId) === 0)
                throw new InvalidGuidException("The guid [$groupId] is not a valid guid and cannot be added asa group for the role [$name].");
        }
        $this->groupIds = $groupIds;
    }

    /**
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function groupIds() : array
    {
        return $this->groupIds;
    }




}