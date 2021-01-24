## Info

**Clientseite JavaScript Clients müssen den Access Type Public haben.**

Die Info zur Erstellung eines Clientseitigen JavaScript habe ich von der Keycloak Dokumentation: [Link](https://www.keycloak.org/docs/latest/securing_apps/#_javascript_adapter) 
Die Library bzw. der Adapter, kann unter `http://IP-Adresse:8080/auth/js/keycloak.js` heruntergeladen werden bzw. unter diesem Link in das Script eingebunden werden.

## Javascript Demo

#### keycloak.json

Unter der Datei *keycloak.json* werden die Daten von dem Keycloak Server angegeben. **Hierbei ist es wichtig dass der Eintrag "ssl-required" auf all gesetzt ist, wenn die Demo/Anwendung Online verfügbar ist**.  Des Weiteren müssen die Werte der Datei auf die eigene Anwendung geändert werden bzw. auf die richtigen Werte geändert werden.
Aufbau einer keycloak.json Datei für Javascript Clientseitige Anwendung:

````json
{
  "realm": "<realmname>",
  "auth-server-url": "http://<Ip-Adresse>:8080/auth/",
  "ssl-required": "extern",
  "resource": "<Client-ID>",
  "public-client":true
}
````

#### app.js

Die *keycloak.json* Datei wird eingebunden wenn man ein Keycloak Objekt erzeugt. In unserem Beispiel passiert das in der Datei app.js 

````javascript
var keycloak = new Keycloak();
````

Ist die *keycloak.json* Datei nicht im selben Ordner wie die JavaScript Datei  oder hat einen anderen Namen, so muss man den Pfad als Parameter übergeben bei der Objekterzeugung.

````javascript
var keycloak = new Keycloak("./directory/keycloak.json");
````

Die Methoden für das Authentifizieren etc. wird von der oben genannten Script Datei (*keycloak.js*) zur Verfügung gestellt.

Für mehr Infos siehe die Dokumentation zu dem JavaScript Adapter [Link](https://www.keycloak.org/docs/latest/securing_apps/#_javascript_adapter) 