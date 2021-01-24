var express = require('express');
var session = require('express-session');
var bodyParser = require('body-parser');
var Keycloak = require('keycloak-connect');
var cors = require('cors');
var path = require('path');

var app = express();
app.use(bodyParser.json());
app.set("view engine","ejs");

// Enable CORS support
app.use(cors());

// Create a session-store to be used by both the express-session
// middleware and the keycloak middleware.

var memoryStore = new session.MemoryStore();

app.use(session({
  secret: 'some secret',
  resave: false,
  saveUninitialized: true,
  store: memoryStore
}));

// Provide the session store to the Keycloak so that sessions
// can be invalidated from the Keycloak console callback.
//
// Additional configuration is read from keycloak.json file
// installed from the Keycloak web console.

var keycloak = new Keycloak({
  store: memoryStore
});

app.use(keycloak.middleware({
  logout: '/logout',
  admin: '/'
}));

app.get('/', function (req, res) {
   res.sendFile(path.join(__dirname+'/views/public.html'));

});

app.get('/user',keycloak.protect(),function(req,res) {
   //Damit wird der aktive Token angezeigt und all seine Daten
   //console.log(req.kauth.grant);
   res.render("user", {
   	username: req.kauth.grant.access_token.content.preferred_username,
    });

});

app.get('/schueler', keycloak.protect('realm:Schueler'), function (req, res) {
   res.render("schueler", {
	username:req.kauth.grant.access_token.content.preferred_username,
});
});

app.get('/lehrer', keycloak.protect('realm:Lehrer'), function (req, res) {
   res.render("lehrer", {
	username:req.kauth.grant.access_token.content.preferred_username,
    });
    
});

app.use('*', function (req, res) {
  res.send('Not found!');
});

app.listen(8082, function () {
  console.log('Started at port 8082');
});
