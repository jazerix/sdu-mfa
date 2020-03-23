<?php

namespace SDU\MFA;

use GuzzleHttp\Client;
use SDU\MFA\Azure\Graph;
use SDU\MFA\Azure\User;

class AuthenticationService
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * AuthenticationService constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return void
     */
    public function authenticate()
    {
        $this->redirect();
    }


    private function redirect()
    {
        header("Location: " . $this->redirectUri());
        exit();
    }

    private function redirectUri()
    {
        $clientId = $this->configuration->clientId();
        $tenantId = $this->configuration->tenantId();
        $redirectUri = $this->urlOrigin();
        $state = $this->state();
        return "https://login.microsoftonline.com/$tenantId/oauth2/authorize?client_id=$clientId&response_type=code&redirect_uri=$redirectUri&response_mode=query&state=$state&resource=https://graph.microsoft.com";
    }

    private function urlOrigin()
    {
        $ssl = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = (( ! $ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    private function requestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    private function stateId()
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime() * 1000000);
        $i = 0;
        $token = '';
        while ($i <= 7)
        {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $token = $token . $tmp;
            $i++;
        }

        return $token;
    }

    private function state()
    {
        $stateId = $this->stateId();
        $_SESSION['state_id'] = $stateId;
        $state = [
            'id'  => $stateId,
            'uri' => $this->requestUri()
        ];
        return base64_encode(json_encode($state));
    }

    public function handleCallback()
    {
        if ($response = $this->isCallback())
            $this->logUserIn($response);
    }

    /**
     * @return bool|array returns false if this request could not be verified to be the callback and the redirect uri
     * if it matches the state.
     */
    public function isCallback()
    {
        if ( ! array_key_exists('QUERY_STRING', $_SERVER))
            return false;
        parse_str($_SERVER['QUERY_STRING'], $query);
        if ( ! (array_key_exists('state', $query) && array_key_exists('code', $query)))
            return false;
        if ( ! array_key_exists('state_id', $_SESSION))
            return false;
        $sessionId = $_SESSION['state_id'];
        unset($_SESSION['state_id']);

        $state = json_decode(base64_decode($query['state']), true);
        if ($state === null)
            return false;
        if ($sessionId != $state['id'])
            return false;
        return [
            'redirect' => $state['uri'],
            'code'     => $query['code']
        ];
    }

    private function accessToken($code)
    {
        $client = new Client();
        $clientId = $this->configuration->clientId();
        $tenantId = $this->configuration->tenantId();
        $clientSecret = $this->configuration->clientSecret();
        $redirectUri = $this->urlOrigin();
        $result = $client->post("https://login.microsoftonline.com/$tenantId/oauth2/token", [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => $clientId,
                'code'          => $code,
                'redirect_uri'  => $redirectUri,
                'client_secret' => $clientSecret,
                'resource'      => 'https://graph.microsoft.com'
            ]
        ])->getBody()->getContents();
        $result = json_decode($result, true);
        return $result['access_token'];
    }

    /**
     * @param $response
     */
    public function logUserIn($response)
    {
        $accessToken = $this->accessToken($response['code']);
        $graph = new Graph($accessToken);
        $user = $graph->me();
        $memberShips = $graph->isMemberOf($this->configuration->roles()->combinedGroupIds());
        $roles = $this->configuration->roles()->matchingRoles($memberShips);
        $user->attachRoles($roles);
        $_SESSION['mfa_user'] = $user;
        header("Location: " . $response['redirect']);
    }

    public function logoutRedirect()
    {
        $clientId = $this->configuration->clientId();
        $tenantId = $this->configuration->tenantId();
        $url = "https://login.microsoftonline.com/$tenantId/oauth2/logout?client_id=$clientId";
        header("Location: $url");
        exit;
    }
}