<?php
require_once 'include/config.php';
include_once 'include/QueryClass.php';
include_once 'include/utils.php';
require_once 'include/bdd.php';

global $pdo;

// Instantiate blog post object
$post = new QueryClass($pdo);

// Receive JSON file and converts it into a PHP object
$data = json_decode(file_get_contents('php://input'), true);

// Verify valid JSON format
if (json_last_error() !== JSON_ERROR_NONE) {
    raise_http_error("Invalid JSON format \n", 400);
}

// Verify and parse request
try {
    $post->verify_and_parse_request($data);
}
catch(Exception $e) {
    raise_http_error($e->getMessage(), $e->getCode());
}

// Verify token
try{
    $post->verify_token($data);
}
catch(Exception $e){
    raise_http_error($e->getMessage(), $e->getCode());
}

// Execute query
$response = null;
switch($post->get_code()){

    case 1:
        // verify query
        try{
            verify_insert_query();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }

        // execute query
        try {
            $post->insert();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }
        break;

    case 3:
        // verify query
        try{
            verify_warehouses_query();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }

        // execute query
        try{
            $response = $post->getWarehouses();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }
        break;

    case 5:
        // verify query
        try {
            verify_products_query();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }

        // execute query
        try{
            $response = $post->getProducts();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }
        break;

    case 7:
        // verify query
        try {
            verify_update_query();
        }
        catch (Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }

        // execute query
        try{
            $response = $post->update();
        }
        catch(Exception $e){
            raise_http_error($e->getMessage(), $e->getCode());
        }
        break;

    default:
        break;
}

// Json Encoding
// Headers
header('Content-Type: application/json');
echo json_encode($response);

// success
die(200);
?>
