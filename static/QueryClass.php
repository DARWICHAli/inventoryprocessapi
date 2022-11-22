<?php

include_once 'jwt.php';
include_once 'utils.php';

use Exception;


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
    public function parse_request($data) {

        if(sizeof($data) != 3){
            throw new Exception("Invalid request format", 400);
        }
        else{
            if( ! array_key_exists("code", $data) || ! array_key_exists("token", $data)
                    || ! array_key_exists("content", $data)){
                throw new Exception("Invalid request format", 400);
            }
            else{
                if(gettype($data['code']) != 'integer' || ! in_array($data['code'], array(1, 3, 4, 7))){
                    throw new Exception("Invalid request code " . $data['code'], 400);
                }
                else if(gettype($data['token']) != 'string'){
                    throw new Exception("Invalid token format", 400);

                }
                else if(gettype($data['content']) != 'array'){
                    throw new Exception("Invalid content format", 400);
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
            throw new Exception('Invalid Token: ' . $e->getMessage(), 401);
        }
    }

    // Demande des noms d'entrepôts
    public function getWarehouses() {

      // Create query
      $query = 'SELECT nom FROM entrepot';
      
      // Prepare statement
      if( ($stmt = $this->conn->prepare($query)) === false ){
        throw new Exception("Invalid request body", 406);
      }

      // Execute query
      if( ! $stmt->execute()){
        throw new Exception("Invalid request body: " . $stmt->error, 406);
      }

      $num = $stmt->rowCount();

      // Post array
      $warehouse_list = array();
      
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
            // à adapter avec les noms des colonnes de la table warehouse
          array_push($warehouse_list, $nom);
        }
      }

      $result = array("list" => $warehouse_list);

      return array("code" => 2, "content" => $result);
    }


    // Demande d'information sur les produits
    // TO DO: revoir les '*' au parsing
    public function getProducts() {

        // Create query
        $query = 'SELECT A.nom as product, S.quantity as quantity, E.nom as warehouse, A.allee as allee, 
                            T.travee as travee, N.niveau as niveau, AV.alveole as alevole
                    FROM stock S 
                            INNER JOIN
                            article A
                            INNER JOIN 
                            entrepot_site ES
                            INNER JOIN
                            entrepot E
                            INNER JOIN
                            allee A
                            INNER JOIN
                            travee T
                            INNER JOIN
                            niveau N
                            INNER JOIN
                            alveole AV
                            ON S.id_article = A.id_article
                            ON S.entrepot_site = ES.entrepot_site
                            ON E.entrepot_id = ES.entrepot_id
                            ON A.id_allee = ES.id_allee
                            ON T.id_travee = ES.id_travee
                            ON N.id_niveau = ES.id_niveau
                            ON AV.id_alveole = ES.id_alveole
                            ';
        
        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false ){
            throw new Exception("Invalid request body", 406);
        } 

        // Execute query
        if(!$stmt->execute()){
            throw new Exception("Invalid request body: " . $stmt->error, 406);
        }

        $num = $stmt->rowCount();

        $products = array();

        if($num > 0){
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

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
                    'name' => $product,
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

    // Ajout d'un produit au stock
    public function insert() {

         // Query
         $query = 'INSERT INTO stock VALUES(:quantity, ES.id_site, AR.id_article)
                    SELECT AR.id_article
                    FROM article AR
                    WHERE AR.code_produit = :product
                    UNION ALL
                    SELECT ES.id_site
                    FROM entrepot_site ES
                    WHERE ES.id_entrepot = :warehouse,
                        ES.id_allee = :allee,
                        ES.id_travee = :travee,
                        ES.id_niveau = :niveau,
                        ES.id_alveole = :alveole
                    ';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $location = $this->content['location'];
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
        else{
            throw new Exception("Invalid request body: " . $stmt_stock->error, 406);
        }
    }

    // Ajustement de stock
    public function update() {
       // Create query
        $query = 'UPDATE stock 
                    SET S.quantity = S.quantity + :quantity,
                    FROM article as A
                    INNER JOIN stock as S
                    INNER JOIN entrepot_site as ES
                    ON A.id_article = S.id_article
                    ON S.id_site = ES.id_site
                    WHERE S.code_produit = :product,
                        ES.id_entrepot = :warehouse,
                        ES.id_allee = :allee,
                        ES.id_travee = :travee,
                        ES.id_niveau = :niveau,
                        ES.id_alveole = :alveole';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $location = $this->content['location'];

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
        else{
            throw new Exception("Invalid request body: " . $stmt->error, 406);
        }
    }
}


?>