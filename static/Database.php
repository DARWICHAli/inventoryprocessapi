<?php

class Database {
    // DB Params
    private $host = 'zefresk.com';
    private $db_name = 'mamazon';
    private $username = 'mamazon_bi';
    private $password = 'ZPrATwoBpxXY8jWZauZLMknY';
    private $port = 3306;
    private $conn;

    // DB Connect
    public function connect() {
      $this->conn = null;

      try { 
        $this->conn = new PDO('mysql:host=' . $this->host . ';port=' . $port . ';dbname=' . $this->db_name, $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Connection Error: ' . $e->getMessage();
      }

      return $this->conn;
    }
  }
?>