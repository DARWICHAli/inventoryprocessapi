<?php

include_once 'jwt.php';
include_once 'utils.php';

class QueryClass {

    // DB stuff
    private $conn;
    private $table;

    // Post Properties
    private $code;
    private $content;
    private $token;
    private $parsed_token;

    // Constructor with DB
    public function __construct($pdo) {
        $this->conn = $pdo;
    }

    public function get_code(){
        return $this->code;
    }

    // Vérifier et parser la requête
    public function verify_and_parse_request($data) {

        $msg = null;

        if(sizeof($data) != 3){
            $msg  = "Invalid request format";
        }
        else{
            if( ! array_key_exists("code", $data) || ! array_key_exists("token", $data)
                    || ! array_key_exists("content", $data)){
                $msg  = "Invalid request format";
            }
            else{
                if(gettype($data['code']) != 'integer' || !in_array($data['code'], array(1, 3, 4, 7))){
                    $msg  = "Invalid request code " . $data['code'];
                }
                else if(gettype($data['token']) != 'string'){
                    $msg  = "Invalid token format";
                }
                else if(gettype($data['content']) != 'array'){
                    $msg  = "Invalid content format";
                }
            }
        }

        $this->code = $data['code'];
        $this->token = $data['token'];
        $this->content = $data['content'];

        return $msg;
    }

    public function verify_token(){

        $msg = null; 

        try{
            $this->parsed_token = parse_token($this->token);
        }
        catch(Exception $e){
            $msg = 'Invalid Token: ' . $e->getMessage();
        }

        return $msg;
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

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
            // à adapter avec les noms des colonnes de la table warehouse
          array_push($warehouse_list, $warehouse_name);
        }
      }

      $result = array("list" => $warehouse_list);

      return array("code" => 2, "content" => $result);
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
      
        $result = array('list' => $products);

        return array("code" => 4, "content" => $result);
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
            return array("code" => 6, "content" => array("success" => 1, "message" => ""));
        }

        return array("code" => 6, "content" => array("success" => 0, "message" => $stmt->error));
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
            return array("code" => 6, "content" => array("success" => 1, "message" => ""));
        }

        return array("code" => 6, "content" => array("success" => 0, "message" => $stmt->error));
    }
}


?>