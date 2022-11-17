<?php
require_once 'secrets.php';

require '../vendor/autoload.php';

use Firebase\JWT\JWT; /* REQUIS */


/* Gestion des JWT basée sur Firebase */

/* Crée un JWT
 * @param username Nom d'utilisateur du récepteur.
 * @param privileges Niveau de privilèges du récepteur.
 * @param seconds Durée de vie du jeton (4 heures par défaut).
 *
 * @return Un JWT sous forme d'une chaîne de caractères.
 */
function create_token($username, $privileges, $seconds = 60 * 60 * 4): string {
	global $private_key;

	// Création des estampilles
	$iat = time();
	$exp = $iat + $seconds;

	$token_payload = [
	'iss' => 'mamazon.zefresk.com',
	'sub' => $username,
	'aud' => $privileges,

	'iat' => $iat,
	'exp' => $exp, // 12h
	'jti' => base64_encode(random_bytes(8))
	];
	JWT::$leeway = 60;
	return JWT::encode($token_payload, $private_key, 'RS256');
}


/* Parse un JWT et émet une exceptio s'il est invalide
 * @param raw_token JWT sous forme de chaîne de caractère
 *
 * @return Le payload du JWT sous forme d'un dictionnaire
 *
 * Si le jeton est invalide, émet une exception.
 */
function parse_token($raw_token): array{
	global $public_key;

	JWT::$leeway = 60;
	return (array)JWT::decode($raw_token, $public_key);
}
