<?php

    require_once 'bdd.php';

    global $pdo;

    // Headers
    header('Content-Type: application/json');

    include_once 'Database.php';
    include_once 'QueryClass.php';

    // Instantiate blog post object
    $post = new QueryClass($$pdo);

    // Receive JSON file and converts it into a PHP object
    // TEST
    $data = json_decode('{
        "code": 3,
        "token": "VALID",
        "content": {
        }
    }');//json_decode(file_get_contents('php://input'), true);

    // Parse query
    $post->parse_query($data);

    // Verif Token ?

    // Execute query 
    $result = $post->execute_query();

    // Json Encoding
    echo json_encode($result);
?>
