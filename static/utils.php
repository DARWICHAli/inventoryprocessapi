<?php 

function verify_warehouses_query(){
    if(sizeof($content) != 0){
        return 1;
    }
    return 0;
}

function verify_products_query($content){

    if(sizeof($content) != 2){
        return 1;
    }

    if(( ! array_key_exists("location", $content) || ! array_key_exists("product", $content))){
        return 1;
    }
    else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'){
        return 1;
    }

    else {
        return 1; $this->verify_location($content['location']);
    }

    return 0;
}

function verify_update_insert_query($content){
    
    if(sizeof($content) != 3){
        return 1;
    }
    else if( ! array_key_exists("location", $content) || ! array_key_exists("product", $content)
            || ! array_key_exists("newqt", $content)){
                
        return 1;
    }
    else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'
            || gettype($content['newqt']) != 'integer'){
                
        return 1;
    }
    else {
        return $this->verify_location($content['location']);
    }
    
    return 0;
}

function verify_location($location){

    if(! array_key_exists("warehouse", $location) || ! array_key_exists("allee", $location)
            || ! array_key_exists("travee", $location) || ! array_key_exists("niveau", $location)
            || ! array_key_exists("alveole", $location)){
                
        return 1;
    }
    else if(gettype($location['warehouse']) != 'string' || gettype($location['allee']) != 'string'
            || gettype($location['travee']) != 'string' || gettype($location['niveau']) != 'string'
            || gettype($location['alveole']) != 'string'){
            
        return 1;
    }
    return 0;
}

function raise_https_error($msg, $error){
    echo $msg;
    die($error);
}

?>