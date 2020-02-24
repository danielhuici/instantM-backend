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
    public function storeUser($name, $mail, $password) {
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
     * Obtener usuario por usuario y contraseña
	 * return: $user
     */
    public function getUserByEmailAndPassword($mail, $password) {
  
        $stmt = $this->conn->prepare("SELECT * FROM user_account WHERE mail = ?");
  
        $stmt->bind_param("s", $mail);
  
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
     * Comprueba si existe usuario
	 * return true/false
     */
    public function isUserExisted($mail) {
        $consulta = $this->conn->prepare("SELECT mail from user_account WHERE mail = ?");
        $consulta->bind_param("s", $mail);
        $consulta->execute();
        $consulta->store_result();
  
        if ($stmt->num_rows > 0) { // Usuario existe
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