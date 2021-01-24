# Info

**PHP Clients in Keycloak müssen den Access Type Confidential haben**

Der Code bzw. das Grundgerüst von dieser Applikation wurde von Steven Maguire erstellt und auf GitHub veröffentlicht: [link](https://github.com/stevenmaguire/oauth2-keycloak) Wir haben diesen Code und die README.md erweitert, siehe folgende Datei für Änderungen: [aenderungen](./CHANGELOG.md)
Jegliche Credits gehen an den Ersteller der unten der Überschrift Credits verlinkt ist, des weiteren kann die Lizenz unten betrachtet werden.

# Keycloak Provider for OAuth 2.0 Client

Dieser Code baut auf dem Package `stevenmaguire/oauth2-keycloaks` auf und ermöglicht die Kommunikation zwischen PHP und Keycloak. ``stevenmaguire/oauth2-keycloaks`` basiert auf dem OAuth 2.0 Provider für PHP von PHP League [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

Zum installieren den Inhalt dieses GitHub kopieren und den folgenden Befehl ausführen im Ordner mit der composer.json Datei:

```bash
composer install
```

## Usage

Verwendung ist die selbe wie bei dem "The League's OAuth client" , als Provider wird folgende Klasse benutzt `\Stevenmaguire\OAuth2\Client\Provider\Keycloak` 

Bei dem Index"authServerUrl" wird die URL des Keycloak Authenticate Servers angegeben, der Aufbau ist wie folgend:  `http://IP-Adresse:8080/auth`. 

Mit dem Index"realm" wird der zu verwendete realm angegeben, im unseren Beispiel: `LogIN-Test`
Mit dem Index "clientId" wird die ClientId die in Keycloak festgelegt wurde, für diese Anwendung, angegeben.
Mit dem Index "clientSecret" wird der Clientsecret angegeben. Siehe Keycloak Client Konfiguration dafür.
Mit dem Index "redirectUri" wird die Seite angegeben wo der User weitergeleitet werden soll nach einer Anmeldung. Sollte immer die Seite sein, die dieses Objekt erstellt.

### Authorization Code Flow

```php
$provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
    'authServerUrl'         => '{keycloak-server-url}',
    'realm'                 => '{keycloak-realm}',
    'clientId'              => '{keycloak-client-id}',
    'clientSecret'          => '{keycloak-client-secret}',
    'redirectUri'           => '{redirect-url}',
    'encryptionAlgorithm'   => null,                             // optional
    'encryptionKeyPath'     => null,                         // optional
    'encryptionKey'         => null     // optional
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
    } catch (Exception $e) {
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: '.$authUrl);
        exit;
    }

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getName());

    } catch (Exception $e) {
        exit('Failed to get resource owner: '.$e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Refreshing a Token

```php
$provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
    'authServerUrl'     => '{keycloak-server-url}',
    'realm'             => '{keycloak-realm}',
    'clientId'          => '{keycloak-client-id}',
    'clientSecret'      => '{keycloak-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

$token = $provider->getAccessToken('refresh_token', ['refresh_token' => $token->getRefreshToken()]);
```

### More Infos

Für weitere Informationen siehe die README.md Datei von Steven Maguire: [README](https://github.com/stevenmaguire/oauth2-keycloak/blob/master/README.md)

## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [Martin Stefan](https://github.com/mstefan21)

## License

Hier ist der Link zu der Lizenz von dem originalen Ersteller:

[License File](https://github.com/stevenmaguire/oauth2-keycloak/blob/master/LICENSE)