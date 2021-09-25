<?php
require('../template/top.php');
$auth_result = auth(1);

if (!is_array($auth_result)) {
	header("Location: /auth/login?returnto=" . $_SERVER['REQUEST_URI']);
	die();
}

$returnto = null;
if (isset($_GET['returnto'])) {
    $returnto = $_GET['returnto'];
}

setcookie("DISCORD_AUTH_SECONDARY_REDIRECT", $returnto);

header("Location: https://discordapp.com/api/oauth2/authorize?client_id=" . DISCORD_APP_CLIENT_ID .
    "&redirect_uri=https%3A%2F%2F" . WEBSITE_DOMAIN . "%2Fauth%2Fdiscord" .
    "&response_type=code" .
    "&scope=identify%20guilds.join");