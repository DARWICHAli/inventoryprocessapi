<?php

class QueryClass {

    // DB stuff
    private $connection;
    private $table;

    // Post Properties
    public $code;
    public $content;
    public $token;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Parser la requête
    public function parse_query($data){
        $this->code = $data->{'code'};
        $this->token = $data->{'token'};
        $this->content = $data->{'content'};
    }

    public function verify_token(){

    }

    // Execution des requêtes
    public function execute_query(){

        $response;

        // Ajout d'un objet
        if($this->code == 1){
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
            $content = $this->getWarehouses();
            $response = array("code" => 2, "content" => $content);
        }

        // Demande d'informations sur les produits
        else if($this->code == 5){
            $content = $this->getProducts();
            $response = array("code" => 4, "content" => $content);
        }

        // Ajustement de stock
        else if($this->code == 7){
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

    // Create Post
    public function create() {

        // Create query
        $query = '';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content->{'product'}));
        $quantity = htmlspecialchars(strip_tags($this->content->{'quantity'}));
        $location = htmlspecialchars(strip_tags($this->content->{'location'}));

        $warehouse = htmlspecialchars(strip_tags($location->{'warehouse'}));
        $allee = htmlspecialchars(strip_tags($location->{'allee'}));
        $travee = htmlspecialchars(strip_tags($location->{'travee'}));
        $niveau = htmlspecialchars(strip_tags($location->{'niveau'}));
        $alveole = htmlspecialchars(strip_tags($location->{'alveole'}));

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

        // Print error if something goes wrong
        // printf("Error: %s.\n", $stmt->error);

        return $stmt->error;
    }

    // Update Post
    public function update() {
       // Create query
        $query = '';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content->{'product'}));
        $quantity = htmlspecialchars(strip_tags($this->content->{'quantity'}));
        $location = htmlspecialchars(strip_tags($this->content->{'location'}));

        $warehouse = htmlspecialchars(strip_tags($location->{'warehouse'}));
        $allee = htmlspecialchars(strip_tags($location->{'allee'}));
        $travee = htmlspecialchars(strip_tags($location->{'travee'}));
        $niveau = htmlspecialchars(strip_tags($location->{'niveau'}));
        $alveole = htmlspecialchars(strip_tags($location->{'alveole'}));

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

        // Print error if something goes wrong
        // printf("Error: %s.\n", $stmt->error);

        return $stmt->error;
    }
}

?>