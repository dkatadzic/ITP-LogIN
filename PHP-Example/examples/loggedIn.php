<html>
  <head>
    <meta charset="UTF-8" />
    <title> Welcome </title>
    <?php

      require 'authenticate.php';
      //print_r($provider->getResourceOwner($token)->toArray());
      $redirectUri = "?redirect_uri=http://192.168.92.133/PHPDemo/oauth2-keycloak/examples/index.html";
      $logouturl=$provider->getBaseLogoutUrl().$redirectUri;
     ?>

     <!-- Style -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  </head>
  <body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color:#8A2BE2">
        <a class="navbar-brand" href="/">PHP Website</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Index</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo($logouturl); ?>">Abmelden</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="container">
      <div style="margin-top:50px">
          <h1> Hallo  <?php printf($username.' : '.$user->getRole()) ?></h1>
          <p class="lead">Sie sind ein eingeloggter User und koennen nichts machen. </p>
      </div>
    </main><!-- /.container -->



     <!-- Scripts -->
     <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  </body>
</html>
