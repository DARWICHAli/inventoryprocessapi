<?php 

$data = json_decode('{  "code": 5, 
                        "token" : "JWT",
                        "content": { 
                            "location": {
                                "warehouse": "MAG1",
                                "allee": "*",
                                "travee": "*",
                                "niveau": "*",
                                "alveole": "*"
                            },
                            "product": "*"
                        }
                    }', true);
                
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'invalid json', "\n";
    die(400);
}

if(sizeof($data) != 3){
    echo 'invalid', "\n";
    die(400);
}
else{
    if( ! array_key_exists("code", $data) || ! array_key_exists("token", $data)
    || ! array_key_exists("content", $data)){
        echo 'invalid key', "\n";
        die(400);
    }
    else{

        if(gettype($data['code']) != 'integer' || !in_array($data['code'], array(1, 3, 5, 7))){
            echo 'invalid code ', $data['code'], "\n";
            die(400);
        }
        else if(gettype($data['token']) != 'string'){
            echo 'invalid token ', "\n";
            die(400);
        }
        else if(gettype($data['content']) != 'array'){
            echo 'invalid query ', "\n";
                    die(400);
        }

        // verify query
        else{
            $content =  $data['content'];
            
            if($data['code'] == 1 || $data['code'] == 7){
                    
                if(sizeof($content) != 3){
                    echo 'invalid query ', "\n";
                    die(400);
                }
                else if( ! array_key_exists("location", $content) || ! array_key_exists("product", $content)
                        || ! array_key_exists("newqt", $content)){
                            echo 'invalid query', "\n";
                            die(400);
                }
                else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'
                        || gettype($content['newqt']) != 'integer'){
                            echo 'invalid query', "\n";
                            die(400);
                }
                else {
                    $location = $content['location'];

                    if(! array_key_exists("warehouse", $location) || ! array_key_exists("allee", $location)
                        || ! array_key_exists("travee", $location) || ! array_key_exists("niveau", $location)
                        || ! array_key_exists("alveole", $location)){
                            echo 'invalid query', "\n";
                            die(400);
                        }
                    else if(gettype($location['warehouse']) != 'string' || gettype($location['allee']) != 'string'
                        || gettype($location['travee']) != 'string' || gettype($location['niveau']) != 'string'
                        || gettype($location['alveole']) != 'string'){
                            echo 'invalid query', "\n";
                            die(400);
                        }
                }
                
            }  
            else if($data['code'] == 3){

                if(sizeof($content) != 0){
                    echo 'invalid query ', "\n";
                    die(400);
                }
            }
            else if($data['code'] == 5){

                if(sizeof($content) != 2){
                    echo 'invalid query ', "\n";
                    die(400);
                }

                if(( ! array_key_exists("location", $content) || ! array_key_exists("product", $content))){
                    echo 'invalid query ', "\n";
                    die(400);
                }
                else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'){
                    echo 'invalid query ', "\n";
                    die(400);
                }

                else {
                    $location = $content['location'];

                    if(! array_key_exists("warehouse", $location) || ! array_key_exists("allee", $location)
                        || ! array_key_exists("travee", $location) || ! array_key_exists("niveau", $location)
                        || ! array_key_exists("alveole", $location)){
                            echo 'invalid query', "\n";
                            die(400);
                        }
                    else if(gettype($location['warehouse']) != 'string' || gettype($location['allee']) != 'string'
                        || gettype($location['travee']) != 'string' || gettype($location['niveau']) != 'string'
                        || gettype($location['alveole']) != 'string'){
                            echo 'invalid query', "\n";
                            die(400);
                        }
                }

            }        
        }
    }
}
?>