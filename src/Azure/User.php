<?php

namespace SDU\MFA\Azure;

use SDU\MFA\Role;
use SDU\MFA\RoleCollection;

class User
{
    private $id;

    private $displayName;

    private $givenName;

    private $surname;

    private $jobTitle;

    private $mail;

    private $officeLocation;

    private $preferredLanguage;

    private $userPrincipalName;

    private $mobilePhone;

    /** @var RoleCollection */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new RoleCollection();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param mixed $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getOfficeLocation()
    {
        return $this->officeLocation;
    }

    /**
     * @param mixed $officeLocation
     */
    public function setOfficeLocation($officeLocation)
    {
        $this->officeLocation = $officeLocation;
    }

    /**
     * @return mixed
     */
    public function getPreferredLanguage()
    {
        return $this->preferredLanguage;
    }

    /**
     * @param mixed $preferredLanguage
     */
    public function setPreferredLanguage($preferredLanguage)
    {
        $this->preferredLanguage = $preferredLanguage;
    }

    /**
     * @return mixed
     */
    public function getUserPrincipalName()
    {
        return $this->userPrincipalName;
    }

    /**
     * @param mixed $userPrincipalName
     */
    public function setUserPrincipalName($userPrincipalName)
    {
        $this->userPrincipalName = $userPrincipalName;
    }

    /**
     * @return mixed
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param mixed $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * @param Role[] $roles
     */
    public function attachRoles(array $roles)
    {
        foreach ($roles as $role)
        {
            $this->roles->add($role);
        }
    }

    public function roles()
    {
        return $this->roles;
    }

    public function is(array $roles) : bool
    {
        return count($roles) == count(array_intersect($roles, $this->roles()->names()));
    }

    public function isNot(array $roles) : bool
    {
        return ! $this->is($roles);
    }
}