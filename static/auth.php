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
echo("DEBUG: _POST = \n");
var_dump($_POST);
echo("\nDEBUG: username: ");
var_dump($username);
echo("\nDEBUG: password: ");
var_dump($password);

if ($username == '') {
	http_response_code(400);
	die("field 'username' is required");
}

if ($password == '') {
	http_response_code(400);
	die("field 'password' is required");
}

/* DEBUG ONLY */
$req = $pdo->prepare('SELECT * FROM utilisateurs');
$req->execute();
$res = $req->fetchAll(PDO::FETCH_ASSOC);
echo("\nDEBUG: all users: ");
print_r($res);

/* Génération d'un hash SHA3-512 salé */
$salted_password = "mamazon.zefresk.com#" . $_POST['password'];
$hashed_password = hash('sha3-512', $salted_password, true);

/* Préparation de la requête */
$prep = $pdo->prepare('SELECT id_utilisateur, privileges FROM utilisateurs WHERE login=:username AND hpass=:hpass');
$prep->bindValue('username', $username);
$prep->bindValue('hpass', $hashed_password);

/* DEBUG */
echo("\nDEBUG: salted password: ");
var_dump($salted_password);
echo("\nDEBUG: hashed password: ");
var_dump($hashed_password);
echo("\nDEBUG: hashed password (as hex): ");
var_dump(bin2hex($hashed_password));
echo("\nDEBUG: hexa hash: ");
var_dump(hash('sha3-512', $salted_password, false));

/* Exécution */
$prep->execute();

/* Récupération */
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
