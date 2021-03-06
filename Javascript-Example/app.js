var keycloak = new Keycloak();
var serviceUrl = 'http://192.168.92.133/JavascriptDemo'

function notAuthenticated() {
    document.getElementById('not-authenticated').style.display = 'block';
    document.getElementById('authenticated').style.display = 'none';
}

function authenticated() {
    document.getElementById('not-authenticated').style.display = 'none';
    document.getElementById('authenticated').style.display = 'block';
	var role= 'normal user';
	if(keycloak.hasRealmRole('Schueler')) {
		role = 'Schueler';
	} else if(keycloak.hasRealmRole('Lehrer')) {
		role = 'Lehrer';
	}
    document.getElementById('message').innerHTML = 'User: ' + keycloak.tokenParsed['preferred_username'] + '</br> Sie sind ein ' + role;
}

function request(endpoint) {
    var req = function() {
        var req = new XMLHttpRequest();
        var output = document.getElementById('message');
        req.open('GET', serviceUrl + '/' + endpoint, true);

        if (keycloak.authenticated) {
            req.setRequestHeader('Authorization', 'Bearer ' + keycloak.token);
        }

        req.onreadystatechange = function () {
            if (req.readyState == 4) {
                if (req.status == 200) {
                    output.innerHTML = 'Message: ' + JSON.parse(req.responseText).message;
                } else if (req.status == 0) {
                    output.innerHTML = '<span class="error">Request failed</span>';
                } else {
                    output.innerHTML = '<span class="error">' + req.status + ' ' + req.statusText + '</span>';
                }
            }
        };

        req.send();
    };

    if (keycloak.authenticated) {
        keycloak.updateToken(10000).success(req);
    } else {
        req();
    }
}

window.onload = function () {
    keycloak.init({ onLoad: 'check-sso',checkLoginIframeInterval:100000 }).success(function () {
        if (keycloak.authenticated) {
            authenticated();
        } else {
            notAuthenticated();
        }

        document.body.style.display = 'block';
    });
}

keycloak.onAuthLogout = notAuthenticated;
