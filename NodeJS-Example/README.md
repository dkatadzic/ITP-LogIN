## Info

**Serverseitige JavaScript Clients können jeden Access Type haben (Public, Bearer Type oder Confidential), die Entscheidung welches verwendet werden soll, muss selbst evaluiert werden. Für die Demo Anwendung wurde ein Public Type benutzt.** 
Die Informationen zur Erstellung einer Severseitigen Javscript (NodeJS) habe ich von der Keycloak Dokumentation: [link](https://www.keycloak.org/docs/latest/securing_apps/#_nodejs_adapter)

## Node.js Demo

#### keycloak.json

Unter der Datei *keycloak.json* werden die Daten von dem Keycloak Server angegeben. **Hierbei ist es wichtig dass der Eintrag "ssl-required" auf all gesetzt ist, wenn die Demo/Anwendung Online erreichbar ist**. Des Weiteren müssen die Werte der Datei auf die eigene Anwendung geändert werden bzw. auf die richtigen Werte geändert werden.
Aufbau einer keycloak.json Datei für eine Node.js Anwendung(Public Client):

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

In dieser Datei wird die Verbindung mit dem Authentifizierungsserver unternommen und die Ressourcen (Webseiten) werden geschützt. Die Schützung der Ressourcen erfolgt hier mit der Methode `keycloak.protect()`. Um zu prüfen ob ein User angemeldet ist (Rolle des User ist hier egal) werden die folgenden Zeilen benutzt:

````javascript
app.get('/user',keycloak.protect(),function(req,res) {
   //Damit wird der aktive Token angezeigt und all seine Daten
   //console.log(req.kauth.grant);
   res.render("user", {
   	username: req.kauth.grant.access_token.content.preferred_username,
    });

});
````

Um eine Ressource nur für eine bestimmte Role zu schützen werden folgende Zeilen benutzt:

````javascript
app.get('/schueler', keycloak.protect('realm:Schueler'), function (req, res) {
   res.render("schueler", {
	username:req.kauth.grant.access_token.content.preferred_username,
});
});
````

Für mehr Infos bzw. genauere Infos siehe die Keycloak Dokumentation zu Node.js [link](https://www.keycloak.org/docs/latest/securing_apps/#_nodejs_adapter)

#### package.json

Für Keycloak wird das Package *keycloa-connect* gebraucht. Das kann eingebunden werden wie folgt:

````json
 "dependencies": {
       "keycloak-connect": "12.0.2"
   }
````

In unsere Datei wurde es wie folgend eingebunden:

````json
"dependencies": {
    "keycloak-connect": "keycloak/keycloak-nodejs-connect",
    ....
  }
````

#### Installation bzw. Ausführen

Die Demo läuft auf dem Port 8082.

Zuerst muss das Projekt heruntergeladen werden bzw. das GitHub kopiert werden. Danach muss im HauptOrdner der Befehl `npm install` ausgeführt werden, um die notwendigen Packages zu installieren. Um das System zu starten benutzt man den Befehl `npm start`. Die Anwendung ist dann unter `http://ip-Adresse:8082` erreichbar.



