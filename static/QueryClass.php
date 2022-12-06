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
            // à adapter avec les noms des colonnes de la table entrepot
          array_push($warehouse_list, $nom);
        }
      }

      $result = array("list" => $warehouse_list);

      return array("code" => 2, "content" => $result);
    }


    // Demande d'information sur les produits
    public function getProducts() {

        $product = false;
        $warehouse = false;
        $allee = false;
        $travee = false;
        $niveau = false;
        $alveole = false;
        $where = false;
        $first = '';

        // Create query
        $query = 'SELECT A.code_produit as code, A.nom as product, S.quantity as quantity, E.nom as warehouse, A.allee as allee, 
                T.travee as travee, N.niveau as niveau, AV.alveole as alveole
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
                ON AV.id_alveole = ES.id_alveole';


        // concaténations de clauses WHERE si nécessaire

        if( strcmp($content['product'], "*") !== 0 ){
            $query .= ' WHERE A.code_produit = :product';
            $product = true;
            $where = true;
        }

        $location = $content['location'];

        if( strcmp($location['warehouse'], "*") !== 0 ){
            if($where)
                $first = ' AND ';
            else
                $first = ' WHERE ';
            $query .= $first . 'E.nom = :warehouse';
            $warehouse = true;
            $where = true;
        }

        if( strcmp($location['allee'], "*") !== 0 ){
            if($where)
                $first = ' AND ';
            else
                $first = ' WHERE ';
            $query .= $first . 'A.allee = :allee';
            $allee = true;
            $where = true;
        }

        if( strcmp($location['travee'], "*") !== 0 ){
            if($where)
                $first = ' AND ';
            else
                $first = ' WHERE ';
            $query .= $first . 'T.travee = :travee';
            $travee = true;
            $where = true;
        }

        if( strcmp($location['niveau'], "*") !== 0 ){
            if($where)
                $first = ' AND ';
            else
                $first = ' WHERE ';
            $query .= $first . 'N.niveau = :niveau';
            $niveau = true;
            $where = true;
        }

        if( strcmp($location['alveole'], "*") !== 0 ){
            if($where)
                $first = ' AND ';
            else
                $first = ' WHERE ';
            $query .= $first . 'AV.alveole = :alveole';
            $alveole = true;
        }

        
        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false ){
            throw new Exception("Invalid request body", 406);
        } 

        // Clean & Bind data
        $location = $this->content['location'];
    
        if($product){
            $product = htmlspecialchars(strip_tags($this->content['product']));
            $stmt->bindParam(':product', $product);
        }
        if($quantity){
            $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
            $stmt->bindParam(':quantity', $quantity);
        }
        if($warehouse){
            $warehouse = htmlspecialchars(strip_tags($location['warehouse']));
            $stmt->bindParam(':warehouse', $warehouse);
        }
        if($allee){
            $allee = htmlspecialchars(strip_tags($location['allee']));
            $stmt->bindParam(':allee', $allee);
        }
        if($travee){
            $travee = htmlspecialchars(strip_tags($location['travee']));
            $stmt->bindParam(':travee', $travee);
        }
        if($niveau){
            $niveau = htmlspecialchars(strip_tags($location['niveau']));
            $stmt->bindParam(':niveau', $niveau);
        }
        if($alveole){
            $alveole = htmlspecialchars(strip_tags($location['alveole']));
            $stmt->bindParam(':alveole', $alveole);
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

    public function add_article($name, $product, $description){
        // création du produit dans les articles
        $query_article = 'INSERT INTO articles VALUES(:name, :product, :description)';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        // Bind data
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':description', $description);

        // Execute query
        if(!$stmt->execute()) {
            throw new Exception("Invalid request body: " . $stmt_stock->error, 406);
        }
    }

    public function add_to_stock($product, $quantity, $warehouse, $allee, $travee, $niveau, $alveole){

        // ajout du produit au stock
        $query_stock = 'INSERT INTO stock VALUES(:quantity, ES.id_site, AR.id_article)
                        SELECT AR.id_article
                        FROM article AR
                        WHERE AR.code_produit = :product
                        UNION ALL
                        SELECT ES.id_site
                        FROM entrepot_site ES
                        WHERE ES.id_entrepot = :warehouse
                            AND ES.id_allee = :allee
                            AND ES.id_travee = :travee
                            AND ES.id_niveau = :niveau
                            AND ES.id_alveole = :alveole
                    ';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        // Bind data
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':warehouse', $warehouse);
        $stmt->bindParam(':allee', $allee);
        $stmt->bindParam(':travee', $travee);
        $stmt->bindParam(':niveau', $niveau);
        $stmt->bindParam(':alveole', $alveole);

        // Execute query
        if(!$stmt->execute()) {
            throw new Exception("Invalid request body: " . $stmt_stock->error, 406);
        }
    }

    // Ajout d'un produit
    public function insert() {

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $name = htmlspecialchars(strip_tags($this->content['name']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $description = htmlspecialchars(strip_tags($this->content['description']));
        $location = $this->content['location'];
        $warehouse = htmlspecialchars(strip_tags($location['warehouse']));
        $allee = htmlspecialchars(strip_tags($location['allee']));
        $travee = htmlspecialchars(strip_tags($location['travee']));
        $niveau = htmlspecialchars(strip_tags($location['niveau']));
        $alveole = htmlspecialchars(strip_tags($location['alveole']));


        // begin transaction
        $this->conn->autocommit(FALSE);
        $this->conn->begin_transaction();

        try {
            $this->add_article($name, $product, $description);
            
            // verify if location is availabe ??
            $this->add_to_stock($product, $quantity, $warehouse, $allee, $travee, $niveau, $alveole);
            $id_stock = $this->conn->lastInsertId();

            $this->add_transaction($id_stock, $quantity);

            $this->conn->commit();
            return array("code" => 0, "content" => array("success" => 1, "message" => ""));
        } 
        catch(Exception $e){
            $this->conn->rollback();
            throw new Exception($e);
        }
        
    }


    public function modify_quantity($product, $quantity, $warehouse, $allee, $travee, $niveau, $alveole){
        
        // Create query
        $query = 'UPDATE stock 
                    SET S.quantity = S.quantity + :quantity,
                        id_stock = LAST_INSERT_ID(id_stock)
                    FROM article A
                    INNER JOIN stock S
                    INNER JOIN entrepot_site ES
                    ON A.id_article = S.id_article
                    ON S.id_site = ES.id_site
                    WHERE S.code_produit = :product
                        AND ES.id_entrepot = :warehouse
                        AND ES.id_allee = :allee
                        AND ES.id_travee = :travee
                        AND ES.id_niveau = :niveau
                        AND ES.id_alveole = :alveole
                    SELECT LAST_INSERT_ID()';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        // Bind data
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':warehouse', $warehouse);
        $stmt->bindParam(':allee', $allee);
        $stmt->bindParam(':travee', $travee);
        $stmt->bindParam(':niveau', $niveau);
        $stmt->bindParam(':alveole', $alveole);

        // Execute query
        if(!$stmt->execute()) {
            throw new Exception("Invalid request body: " . $stmt->error, 406);
        }
    }

    // Ajustement de stock
    public function update() {

        // Clean data
        $product = htmlspecialchars(strip_tags($this->content['product']));
        $quantity = htmlspecialchars(strip_tags($this->content['quantity']));
        $location = $this->content['location'];
        $warehouse = htmlspecialchars(strip_tags($location['warehouse']));
        $allee = htmlspecialchars(strip_tags($location['allee']));
        $travee = htmlspecialchars(strip_tags($location['travee']));
        $niveau = htmlspecialchars(strip_tags($location['niveau']));
        $alveole = htmlspecialchars(strip_tags($location['alveole']));


        // begin transaction
        $this->conn->autocommit(FALSE);
        $this->conn->begintransaction();

        try{
            $this->modify_quantity($product, $quantity, $warehouse, $allee, $travee, $niveau, $alveole);
            $id_stock = $this->conn->lastInsertId();
            $this->add_transaction($id_stock, $quantity);
            
            $this->conn->commit();
            return array("code" => 6, "content" => array("success" => 1, "message" => ""));
        }
        catch(Exception $e){
            $this->conn->rollback();
            throw new Exception($e);
        }
    }

    public function add_transaction($id_stock, $delta){

        if( ($stmt_id_site = $query_id_site = $pdo->prepare("SELECT id_site, id_article FROM stock WHERE id_stock = " . $id_stock)) === false){
            throw new Exception("Save Transaction Failed: " . $stmt_id_site->error , 406);
        }

        if( ! $query_id_site->execute()){
            throw new Exception("Save Transaction Failed: " . $stmt_id_site->error, 406);
        }

        $row = $query_id_site->fetch();

        // récupère id_site et id_article dans des variables $id_site et $id_article
        extract($row);

        $query = 'INSERT INTO transaction VALUES(:id_utilisateur, :id_article, :id_site, :delta)';

        // Prepare statement
        if( ($stmt = $this->conn->prepare($query)) === false){
            throw new Exception("Invalid request body", 406);
        }

        $id_utilisateur = $this->parsed_token->{'sub'};

        // Bind data
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_article', $id_article);
        $stmt->bindParam(':id_site', $id_site);
        $stmt->bindParam(':delta', $delta);

        // Execute query
        if( ! $stmt->execute()) {
            throw new Exception("Save Transaction Failed: " . $stmt->error, 406);
        }
    }
}


?>