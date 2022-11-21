<?php

/*
 * Génère un JWT à partir d'un requête POST ['username' = xx, 'password' = yy]
 */

require_once 'include/config.php';
require_once 'include/bdd.php';
require_once 'include/jwt.php';

global $pdo;

function get_var($var, $str, $default = '') {
    return (key_exists($str, $var) && $var[$str] != '') ? htmlspecialchars($var[$str]) : $default;
}

$username = get_var($_POST, 'username');
$password = get_var($_POST, 'password');

/* DEBUG */
echo("DEBUG: _POST = ");
var_dump($_POST);
echo("DEBUG: username: ");
var_dump($username);
echo("DEBUG: password: ");
var_dump($password);

if ($username == '') {
	http_response_code(400);
	die("field 'username' is required");
}

if ($password == '') {
	http_response_code(400);
	die("field 'password' is required");
}

/* Génération d'un hash SHA3-512 salé */
$salted_password = "mamazon.zefresk.com#" . $_POST['password'];
$hashed_password = hash('sha3-512', $salted_password, true);

/* Préparation de la requête */
$prep = $pdo->prepare('SELECT id_utilisateur, privileges FROM utilisateurs WHERE login=:username AND hpass=:hpass');
$prep->bindValue('username', $username);
$prep->bindValue('hpass', $hashed_password);

/* DEBUG */
echo("DEBUG: salted password: ");
var_dump($salted_password);
echo("DEBUG: hashed password: ");
var_dump($hashed_password);
echo("DEBUG: hashed password (as hex): ");
var_dump(bin2hex($hashed_password));
echo("DEBUG: hexa hash: ");
var_dump(hash('sha3-512', $salted_password, false));

/* Exécution */
$ret = $prep->fetch(PDO::FETCH_ASSOC);
if (!$ret) {
	http_response_code(403);
	die("Authentification failed");
} else {
	$privileges = $ret['privileges'];
	$uid = $ret['id_utilisateur'];
	$jwt = create_token($username, $uid, $privileges);
	echo($jwt);
	http_response_code(200);
}

?>
