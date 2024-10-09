<?php

require __DIR__ . "/vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("358203597193-t6dt3a3trhphu50m1jr1lp75gqbdit1v.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Login Example</title>
</head>
<body>

    <a href="<?= $url ?>">Sign in with Google</a>

</body>
</html>