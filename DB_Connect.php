<?php
class DB_Connect {
    private $conn;
  
   // Conexión a la base de datos (read Config.php)
   public function connect() {
       require_once 'include/Config.php';
          
   // Conexión a la base de datos (mysqli)
   $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
          
        // return handler
        return $this->conn;
    }
}
  
?>