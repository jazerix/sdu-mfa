<?php

namespace SDU\MFA\Azure;

use GuzzleHttp\Client;

class Graph
{
    private $httpClient;

    public function __construct($accessToken)
    {
        $this->httpClient = new Client(
            [
                'headers'  => [
                    'Authorization' => "Bearer $accessToken"
                ],
                'base_uri' => 'https://graph.microsoft.com/v1.0/'
            ]);
    }

    public function me() : User
    {
        $user = $this->requestResource('me');
        $me = new User();
        $me->setId($user['id']);
        $me->setUserPrincipalName($user['userPrincipalName']);
        $me->setSurname($user['surname']);
        $me->setPreferredLanguage($user['preferredLanguage']);
        $me->setOfficeLocation($user['officeLocation']);
        $me->setMobilePhone($user['mobilePhone']);
        $me->setMail($user['mail']);
        $me->setJobTitle($user['jobTitle']);
        $me->setGivenName($user['givenName']);
        $me->setDisplayName($user['displayName']);

        return $me;
    }

    public function isMemberOf(array $groupIds)
    {
        $memberships = json_decode($this->httpClient->post('me/checkMemberGroups', [
            'json' => [
                'groupIds' => array_values($groupIds)
            ]
        ])->getBody()->getContents(), true);
        return array_intersect($groupIds, $memberships['value']);
    }

    private function requestResource($uri)
    {
        return json_decode($this->httpClient->get($uri)->getBody()->getContents(), true);
    }
}