<?php

require '../vendor/autoload.php';

session_start();
$username = null;
$provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
    'authServerUrl'             => 'http://192.168.92.132:8080/auth',
    'realm'                     => 'LogIN-Test',
    'clientId'                  => 'PHPDemo',
    'clientSecret'              => 'bdb36b92-4992-49fa-b4be-12614bb88ddc',
    'redirectUri'               => 'http://192.168.92.133/PHPDemo/oauth2-keycloak/examples/loggedIn.php',
    'encryptionAlgorithm'       => null,
    'encryptionKey'             => null,
    'encryptionKeyPath'         => null
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state, make sure HTTP sessions are enabled.');
} else {
    // Try to get an access token (using the authorization coe grant)
    try {

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

         // echo 'Access Token: ' . $token->getToken() . "<br>";
         // echo 'Refresh Token: ' . $token->getRefreshToken() . "<br>";
         // echo 'Expired in: ' . $token->getExpires() . "<br>";
         // echo 'Already expired? ' . ($token->hasExpired() ? 'expired' : 'not expired') . "<br>";

    } catch (Exception $e) {
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: '.$authUrl);
        exit;
    }

    // Optional: Now you have a token you can look up a users profile data
    try {
        // We got an access token, let's now get the user's details
        //print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
        //Zwei Rollen derzeit Schueler und Lehrer
        $role="";
        if($provider->hasRole("Schueler",$token)) {
            $role="Schueler";
        } elseif($provider->hasRole("Lehrer",$token)) {
            $role="Lehrer";
        }
        $user = $provider->getResourceOwner($token);
		$user->setRole($role);
        // Use these details to create a new profile
        //printf('Hello %s!\n<br>', $user->getName());
        $username=$user->getName();
    } catch (Exception $e) {
        exit('Failed to get resource owner: '.$e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    //echo $token->getToken();
}
