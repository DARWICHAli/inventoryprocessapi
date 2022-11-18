<?php
    // Headers
    header('Content-Type: application/json');

    include_once 'QueryClass.php';

    require_once 'config.php';
    require_once 'bdd.php';

    global $pdo;

    // Instantiate blog post object
    $post = new QueryClass($pdo);

    // Receive JSON file and converts it into a PHP object
    $data = json_decode(file_get_contents('php://input'), true);

    // Verify valid JSON format
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Invalid JSON format',  "\n";
        die(400);
    }

    // Verify and parse request
    $post->verify_and_parse_request($data);

    // Verify token
    $post->verify_token();

    // Execute query 
    $result = $post->verify_and_execute_query();

    // Json Encoding
    echo json_encode($result);
?>
