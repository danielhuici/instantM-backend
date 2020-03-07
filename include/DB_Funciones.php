<?php
class DB_Funciones {
  
    private $conn;
  
    // Constructor funciones
    function __construct() {
        require_once 'DB_Connect.php';
        // Conectar a la BD
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
  
    // Destructor
    function __destruct() {
          
    }
  
    /**
     * Registrar usuario 
     * return: $user
     */
    public function storeUser($name, $password, $mail) {
        $hash = $this->hashSSHA($password);
        // $encrypted_password = $hash["encrypted"]; // Encriptada en hash (MD5)
  
        $stmt = $this->conn->prepare("INSERT INTO user_account(name, password, mail) VALUES(?, ?, ?)");
		 $stmt->bind_param("sss", $name, $password, $mail);
        // $stmt->bind_param("sss", $name, $encrypted_password, $mail);
        $result = $stmt->execute();
        $stmt->close();
  
        // Creación correcta?
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user_account WHERE mail = ?");
            $stmt->bind_param("s", $mail);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
  
            return $user;
        } else {
            return false;
        }
    }
	
	/**
     * Crear grupo 
     * return: $group
     */
    public function storeGroup($name, $id_creator_user) {  
        $stmt = $this->conn->prepare("INSERT INTO chat_group(name, id_creator_user) VALUES(?, ?)");
		$stmt->bind_param("si", $name, $id_creator_user);
        $result = $stmt->execute();
        $stmt->close();
  
        // Creación correcta?
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM chat_group WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $group = $stmt->get_result()->fetch_assoc();
            $stmt->close();
  
            return $group;
        } else {
            return false;
        }
    }
  
    /**
     * Obtener usuario por usuario y contraseña
	 * return: $user
     */
    public function getUserByEmailAndPassword($name, $password) {
  
        $stmt = $this->conn->prepare("SELECT * FROM user_account WHERE name = ?");
  
        $stmt->bind_param("s", $name);
  
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
  
            // HASH Rules! Para un futuro no muy lejano...
			
            //$salt = $user['salt'];
            $encrypted_password = $user['password'];
            // $hash = $this->checkhashSSHA($salt, $password);
            
            if ($encrypted_password == $password) { // Contraseña correcta?
                return $user;
            }
        } else {
            return NULL;
        }
    }
	
	/**
     * Obtener id por usuario
	 * return: $user
     */
    public function getUserIdByUsername($username) {
  
        $stmt = $this->conn->prepare("SELECT * FROM user_account WHERE name = ?");
        $stmt->bind_param("s", $username);
  
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
  
           return $user;
        } else {
            return NULL;
        }
    }
  
	/**
     * Obtener grupo por id usuario
	 * return: $groups
     */
    public function getGroupsByUserId($id_user) {
        $query =  "SELECT * FROM chat_group WHERE id_chat_group =           
                                            (SELECT id_chat_group FROM user_partof_group WHERE id_user = '$id_user')";
        
        $con = mysqli_connect("34.69.44.48","root","CentralTeruel1.","instantM");
       
        $result = mysqli_query($con, "SELECT name FROM chat_group WHERE id_chat_group IN (SELECT id_chat_group FROM user_partof_group WHERE id_user = $id_user)");

        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        
        return $rows;                                            
    }
	
  
    /**
     * Comprueba si existe usuario
	 * return true/false
     */
    public function isUserExisted($mail) {
        $consulta = $this->conn->prepare("SELECT mail from user_account WHERE mail = ?");
        $consulta->bind_param("s", $mail);
        $consulta->execute();
        $consulta->store_result();
  
        if ($consulta->num_rows > 0) { // Usuario existe
            $consulta->close();
            return true;
        } else { // Usuario no existe
            $consulta->close();
            return false;
        }
    }
  
  // ----- FUNCIONES DE ENCRIPACION, PARA ADELANTE ------ //
  
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
  
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
  
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
  
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
  
        return $hash;
    } 
} 


?>