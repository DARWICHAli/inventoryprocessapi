<?php 


function verify_warehouses_query($content){
    if(sizeof($content) != 0){
        throw new Exception("Request format is invalid: wrong number of entries", 400);
    }
}

function verify_products_query($content){

    if(sizeof($content) != 2){
        throw new Exception("Request format is invalid: missing entry", 400);
    }
    if(( ! array_key_exists("location", $content) || ! array_key_exists("product", $content))){
        throw new Exception("Request format is invalid: missing entry", 400);
    }
    else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'){
        throw new Exception("Request format is invalid: invalid typing", 400);
    }
    else {
        try {
            $this->verify_location($content['location']);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}

function verify_update_query($content){
    
    if(sizeof($content) != 3){
        throw new Exception("Request format is invalid: missing entry for update", 400);
    }
    else if( ! array_key_exists("location", $content) || ! array_key_exists("product", $content)
            || ! array_key_exists("quantity", $content)){
                
        throw new Exception("Request format is invalid: missing entry for update", 400);
    }
    else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'
            || gettype($content['quantity']) != 'integer'){
                
        throw new Exception("Request format is invalid: invalid typing", 400);
    }
    else {
        try {
            $this->verify_location($content['location']);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}

function verify_insert_query($content){
    
    if(sizeof($content) != 5){
        throw new Exception("Request format is invalid: missing entry for insert", 400);
    }
    else if( ! array_key_exists("location", $content) || ! array_key_exists("product", $content)
            || ! array_key_exists("quantity", $content) || ! array_key_exists("name", $content)
            || ! array_key_exists("description", $content)){
                
        throw new Exception("Request format is invalid: missing entry for insert", 400);
    }
    else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'
            || gettype($content['quantity']) != 'integer' || gettype($content['name']) != 'string'
            || gettype($content['description']) != 'string'){
                
        throw new Exception("Request format is invalid: invalid typing", 400);
    }
    else {
        try {
            $this->verify_location($content['location']);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}

function verify_location($location){

    if(! array_key_exists("warehouse", $location) || ! array_key_exists("allee", $location)
            || ! array_key_exists("travee", $location) || ! array_key_exists("niveau", $location)
            || ! array_key_exists("alveole", $location)){
                
        throw new Exception("Request format is invalid: missing location entry", 400);
    }
    else if(gettype($location['warehouse']) != 'string' || gettype($location['allee']) != 'string'
            || gettype($location['travee']) != 'string' || gettype($location['niveau']) != 'string'
            || gettype($location['alveole']) != 'string'){
            
        throw new Exception("Request format is invalidt: invalid typing in location", 400);
    }
}

function raise_http_error($msg, $error){
    echo $msg;
    die($code);
}

?>