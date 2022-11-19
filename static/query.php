<?php
    // Headers
    header('Content-Type: application/json');

    include_once 'QueryClass.php';
    include_once 'utils.php';

    require_once 'config.php';
    require_once 'bdd.php';

    global $pdo;

    // Instantiate blog post object
    $post = new QueryClass($pdo);

    // Receive JSON file and converts it into a PHP object
    $data = json_decode(file_get_contents('php://input'), true);

    // Verify valid JSON format
    if (json_last_error() !== JSON_ERROR_NONE) {
        raise_https_error("Invalid JSON format \n", 400);
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
    $response;
    switch($post->get_code()){

        case 1:
            // verify query
            try{
                $post->verify_update_insert_query();
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
                $post->verify_warehouses_query();
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
                $post->verify_products_query();
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
                $post->verify_update_insert_query();
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
    echo json_encode($response);

    // success
    die(200);
?>
