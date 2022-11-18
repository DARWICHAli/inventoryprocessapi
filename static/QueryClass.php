<?php

include_once 'jwt.php';

class QueryClass {

    // DB stuff
    private $conn;
    private $table;

    // Post Properties
    public $code;
    public $content;
    public $token;
    public $parsed_token;

    // Constructor with DB
    public function __construct($pdo) {
        $this->conn = $pdo;
    }

    // Vérifier et parser la requête
    public function verify_and_parse_request($data){

        if(sizeof($data) != 3){
            echo 'invalid request format', "\n";
            die(400);
        }
        else{
            if( ! array_key_exists("code", $data) || ! array_key_exists("token", $data)
                    || ! array_key_exists("content", $data)){
                echo 'invalid request format', "\n";
                die(400);
            }
            else{
                if(gettype($data['code']) != 'integer' || !in_array($data['code'], array(1, 3, 4, 7))){
                    echo 'invalid request code ', $data['code'], "\n";
                    die(400);
                }
                else if(gettype($data['token']) != 'string'){
                    echo 'invalid token format', "\n";
                    die(400);
                }
                else if(gettype($data['content']) != 'array'){
                    echo 'invalid content format', "\n";
                    die(400);
                }
            }
        }

        $this->code = $data['code'];
        $this->token = $data['token'];
        $this->content = $data['content'];
    }

    public function verify_token(){
        try{
            $this->parsed_token = parse_token($this->token);
        }
        catch(Exception $e){
            echo 'Invalid Token:' , $e->getMessage() , '\n';
            die(401);
        }
    }

    // Execution des requêtes
    public function verify_and_execute_query(){

        $response;

        // Ajout d'un objet
        if($this->code == 1){

            $this->verify_update_insert_query();

            $result = $this->insert();
            // succès
            if($result === null){
                $response = array("code" => 0, "content" => array("success" => 1, "message" => ""));
            }
            // echec
            else{
                $response = array("code" => 0, "content" => array("success" => 0, "message" => $result));
            }
        }

        // Demande des noms d'entrepôts
        else if($this->code == 3){

            $this->verify_warehouses_query();

            $content = $this->getWarehouses();
            $response = array("code" => 2, "content" => $content);
        }

        // Demande d'informations sur les produits
        else if($this->code == 5){

            $this->verify_products_query();

            $content = $this->getProducts();
            $response = array("code" => 4, "content" => $content);
        }

        // Ajustement de stock
        else if($this->code == 7){

            $this->verify_update_insert_query();

            $result = $this->update();
            // succès
            if($result === null){
                $response = array("code" => 6, "content" => array("success" => 1, "message" => ""));
            }
            // echec
            else{
                $response = array("code" => 6, "content" => array("success" => 0, "message" => $result));
            }
        }

        return $response;
    }


    // Demande des nims d'entrepôts
    public function getWarehouses() {

      // Create query
      $query = '';
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      $num = $stmt->rowCount();
      // Post array

      $warehouse_list = array();
      
      if($num > 0){

        $count = 0;
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
            // à adapter avec les noms des colonnes de la table warehouse
          array_push($warehouse_list, $warehouse_name);
        }
      }

      return array("list" => $tmp);
    }


    // Get Posts
    public function getProducts() {

        // Create query
        $query = '';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        $num = $stmt->rowCount();
        // Post array

        $products = array();

        if($num > 0){
            
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {

                extract($row);

                $location = array(
                    'warehouse' => $warehouse,
                    'allee' => $allee,
                    'travee' => $travee,
                    'niveau' => $niveau,
                    'alveole' => $alveole,
                );

                $product_item = array(
                    'code' => $code,
                    'name' => $product_name,
                    'quantity' => $quantity,
                    'location' => $location
                );

                // Push to "data"
                array_push($products, $product_item);
            }
        }
      
        return array('list' => $products);
      }

    // Insert Post
    public function insert() {

        // Create query
        $query = '';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $location = htmlspecialchars(strip_tags($this->content['location']));

        $warehouse = htmlspecialchars(strip_tags($location['warehouse']));
        $allee = htmlspecialchars(strip_tags($location['allee']));
        $travee = htmlspecialchars(strip_tags($location['travee']));
        $niveau = htmlspecialchars(strip_tags($location['niveau']));
        $alveole = htmlspecialchars(strip_tags($location['alveole']));

        // Bind data
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':quantity', $quantity);

        $stmt->bindParam(':warehouse', $warehouse);
        $stmt->bindParam(':allee', $allee);
        $stmt->bindParam(':travee', $travee);
        $stmt->bindParam(':niveau', $niveau);
        $stmt->bindParam(':alveole', $alveole);

        // Execute query
        if($stmt->execute()) {
            return null;
        }

        return $stmt->error;
    }

    // Update Post
    public function update() {
       // Create query
        $query = '';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $location = htmlspecialchars(strip_tags($this->content['location']));

        $warehouse = htmlspecialchars(strip_tags($location['warehouse']));
        $allee = htmlspecialchars(strip_tags($location['allee']));
        $travee = htmlspecialchars(strip_tags($location['travee']));
        $niveau = htmlspecialchars(strip_tags($location['niveau']));
        $alveole = htmlspecialchars(strip_tags($location['alveole']));

        // Bind data
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':quantity', $quantity);

        $stmt->bindParam(':warehouse', $warehouse);
        $stmt->bindParam(':allee', $allee);
        $stmt->bindParam(':travee', $travee);
        $stmt->bindParam(':niveau', $niveau);
        $stmt->bindParam(':alveole', $alveole);

        // Execute query
        if($stmt->execute()) {
            return null;
        }

        return $stmt->error;
    }

    public function verify_warehouses_query(){
        if(sizeof($content) != 0){
            echo 'invalid query ', "\n";
            die(400);
        }
    }

    public function verify_products_query(){

        $invalid = 0;

        if(sizeof($content) != 2){
            $invalid = 1;
        }

        if(( ! array_key_exists("location", $content) || ! array_key_exists("product", $content))){
            $invalid = 1;
        }
        else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'){
            $invalid = 1;
        }

        else {
            $invalid = $this->verify_location($content['location']);
        }
        
        if($invalid){
            echo 'invalid query ', "\n";;
            die(400);
        }
    }

    public function verify_update_insert_query(){
        
        $invalid = 0;

        if(sizeof($content) != 3){
            $invalid = 1;
        }
        else if( ! array_key_exists("location", $content) || ! array_key_exists("product", $content)
                || ! array_key_exists("newqt", $content)){
                    
            $invalid = 1;
        }
        else if(gettype($content['location']) != 'array' || gettype($content['product']) != 'string'
                || gettype($content['newqt']) != 'integer'){
                    
            $invalid = 1;
        }
        else {
            $invalid = $this->verify_location($content['location']);
        }
        
        if($invalid){
            echo 'invalid query ', "\n";;
            die(400);
        }
    }

    public function verify_location($location){

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

}


?>