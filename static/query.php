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
    if( ($msg = $post->verify_and_parse_request($data) !== null)){
        raise_https_error($msg, 400);
    }

    // Verify token
    if( ($msg = $post->verify_token($data) !== null)){
        raise_https_error($msg, 401);
    }

    // Execute query 
    $response;
    switch($post->get_code()){

        case 1:
            if($post->verify_update_insert_query()){
                raise_https_error("Invalid request", 400);
            }
            $response = $post->insert();
            break;

        case 3:
            if($post->verify_warehouses_query()){
                raise_https_error("Invalid request", 400);
            }
            $response = $post->getWarehouses();
            break;

        case 5:
            if($post->verify_products_query()){
                raise_https_error("Invalid request", 400);
            }
            $response = $post->getProducts();
            break;

        case 7:
            if($post->verify_update_insert_query()){
                raise_https_error("Invalid request", 400);
            }
            $response = $post->update();
            break;

        default:
            break;
    }

    // Json Encoding
    echo json_encode($response);
    die(200);
?>
