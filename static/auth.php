<?php

/*
 * Génère un JWT à partir d'un requête POST ['username' = xx, 'password' = yy]
 */

require_once 'config.php';
require_once 'bdd.php';
require_once 'jwt.php';

global $pdo;

function get_var($var, $str, $default = '') {
    return (key_exists($str, $var) && $var[$str] != '') ? htmlspecialchars($var[$str]) : $default;
}

$username = get_var($_POST, 'username');
$password = get_var($_POST, 'password');

if ($username == '') {
	echo("field 'username' is required");
	die(400);
}

if ($password == '') {
	echo("field 'password' is required");
	die(400);
}

/* Génération d'un hash SHA3-512 salé */
$salted_password = "mamazon.zefresk.com#" . $_POST['password'];
$hashed_password = hash('sha3-512', $salted_password, true);

/* Préparation de la requête */
$prep = $pdo->prepare('SELECT id_utilisateur, privileges FROM utilisateurs WHERE login=:username AND hpass=:hpass');
$prep->bindValue('username', $username);
$prep->bindValue('hpass', $hashed_password);

/* Exécution */
$ret = $prep->fetch(PDO::FETCH_ASSOC);
if (!$ret) {
	echo("Authentification failed");
	die(403);
} else {
	$privileges = $ret['privileges'];
	$uid = $ret['id_utilisateur'];
	$jwt = create_token($username, $uid, $privileges);
	echo($jwt);
	die(200);
}

?>
