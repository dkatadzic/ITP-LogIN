# Changelog
Änderungen können hier gesehen werden.

## V1.0 - 2020-18-12

### Added
- Zu der Klasse *src/Provider/KeycloakResourceOwner.php* wurde folgendes hinzugefügt:
  - Die Methode `public function setRole($role)`
  - Die Methode `public function getRole()`
- Zu der Klasse *src/Provider/Keycloak.php* wurde die Methode `public function hasRole($specificRole,$token)` hinzugefügt.
  - Der Methode wird eine Keycloak Rolle (z.B: lehrer) und der Acces Token übergeben. Besitzt der User diese bestimmte Role wird true zurückgegeben.

Mit den Änderungen ist es jetzt möglich den User eine Role zu zuweisen, sie abzufragen und zu prüfen ob er eine bestimmte role hat.