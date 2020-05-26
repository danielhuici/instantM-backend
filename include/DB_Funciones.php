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
    public function storeUser($name, $password, $mail, $birthday) {
        $hash = $this->hashSSHA($password);
        // $encrypted_password = $hash["encrypted"]; // Encriptada en hash (MD5)
  
        $stmt = $this->conn->prepare("INSERT INTO user_account(name, password, mail, birth_date) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("ssss", $name, $password, $mail, $birthday);
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


    public function addUserAdmin($id_user, $id_chat_group) {
        $stmt = $this->conn->prepare("INSERT INTO user_manage_group(id_user, id_chat_group) VALUES(?, ?)");
		$stmt->bind_param("ii", $id_user, $id_chat_group);
        $result = $stmt->execute();
        $stmt->close();
    }
   
    public function removeUserAdmin($id_user, $id_chat_group) {
        $stmt = $this->conn->prepare("DELETE FROM user_manage_group WHERE id_user LIKE ? AND id_chat_group LIKE ?");
		$stmt->bind_param("ii", $id_user, $id_chat_group);
        $result = $stmt->execute();
        $stmt->close();
    }/*
    
    public function groupAlreadyExists($id_creator_user, $name){
        $stmt = $this->conn->prepare("SELECT id_chat_group FROM chat_group WHERE name = ? AND id_creator_user = ?");
        $stmt->bind_param("si", $name, $id_creator_user);
        $stmt->execute();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }*/
    public function getAlternativeAdmin($id_user, $id_chat_group){
        $stmt = $this->conn->prepare("SELECT id_user FROM user_partof_group WHERE id_chat_group LIKE ? AND id_user NOT LIKE ?");
        $stmt->bind_param("ii", $id_chat_group, $id_user);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $users['id_user'];
    }
    
    public function isAdmin($id_user, $id_chat_group){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as num FROM user_manage_group WHERE id_chat_group LIKE ? AND id_user LIKE ?");
        $stmt->bind_param("ii", $id_chat_group, $id_user);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();
       if($exists['num'] > 0){
            return true;
       }else{
            return false;
       }
    }
    
    public function changeAdmin($id_admin, $id_new_admin, $id_chat_group){
        $this->addUserAdmin($id_new_admin, $id_chat_group);
        $this->removeUserAdmin($id_admin, $id_chat_group);
    }
    
    public function changeGroupCreator($id_chat_group, $id_new_admin){
        echo $id_new_admin;
        $stmt = $this->conn->prepare("UPDATE chat_group SET id_creator_user = ? WHERE id_chat_group= ? ;");
       
        $null = NULL;
        $stmt->bind_param("ii", $id_new_admin, $id_chat_group);

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

	/**
     * Crear grupo 
     * return: $group
     */
    public function storeGroup($name, $id_creator_user, $group_description, $blob) {
        //$exists = groupAlreadyExists($id_creator_user, $name);
        
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM chat_group WHERE name = ? AND id_creator_user = ?");
        $stmt->bind_param("si", $name, $id_creator_user);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
       if($exists[0] > 0){
            return false;
       }
        $stmt = $this->conn->prepare("INSERT INTO chat_group(name, id_creator_user, description, group_image) VALUES(?, ?, ?, ?);");
       
       
        $null = NULL;
        $stmt->bind_param("sisb", $name, $id_creator_user,  $group_description, $null);
        
        $stmt->send_long_data(3, $blob);
        //return true;
        $result = $stmt->execute();
        $stmt->close();
        
        // Creación correcta?
        if ($result) {
            $stmt = $this->conn->prepare("SELECT id_chat_group FROM chat_group WHERE name = ? AND id_creator_user = ?");
            $stmt->bind_param("si", $name, $id_creator_user);
            $stmt->execute();
            $group = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            
            return $group;
        } else {
            return false;
        }
    }
     
     public function updateGroup($name, $group_description, $blob, $group_id) {
        $stmt = $this->conn->prepare("UPDATE instantM.chat_group SET name= ?, description= ?, group_image= ? WHERE id_chat_group= ? ;");
       
        $null = NULL;
        $stmt->bind_param("ssbi", $name, $group_description, $null, $group_id);
        
        $stmt->send_long_data(2, $blob);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function updateUser($name, $mail, $state, $birth_date, $user_id) {
        $stmt = $this->conn->prepare("UPDATE user_account
                                     SET name= ?,
                                     mail = ?,
                                     state = ?,
                                     birth_date= ?
                                     WHERE id_user= ? ;");

        $stmt->bind_param("ssssi", $name, $mail, $state, $birth_date, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function updatePassword($new_password, $user_id) {
        $stmt = $this->conn->prepare("UPDATE user_account
                                     SET password = ?
                                     WHERE id_user= ? ;");

        $null = NULL;
        $stmt->bind_param("si", $new_password, $user_id);
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
     public function deleteContact($id_creator_user,  $id_contact) {  
        $stmt = $this->conn->prepare("DELETE FROM user_contact_of_user WHERE id_user = ? AND id_contact = ?");
        
        $stmt->bind_param("ss", $id_creator_user,  $id_contact);
        $result = $stmt->execute();
        $stmt->close();
        
        // Creación correcta?
        if ($result) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_contact_of_user WHERE id_user = ? AND id_contact = ?");
            $stmt->bind_param("ss", $id_creator_user,  $id_contact);
            $stmt->execute();
            $deleted = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return !$deleted;
        } else {
            return false;
        }
    }

    
    public function deleteGroup($id_user,  $id_chat_group) {  
        $this->removeGroupAdmins($id_chat_group);
        $this->removeMembers($id_chat_group);
        
        $stmt = $this->conn->prepare("DELETE FROM chat_group WHERE id_chat_group LiKE ? ");
		$stmt->bind_param("i", $id_chat_group);
        $result = $stmt->execute();
        $stmt->close();
        
    }
    public function leaveGroup($id_user,  $id_chat_group) {  
       
        
        if($this->isAdmin($id_user,  $id_chat_group) == true){
           $id_new_admin = $this->getAlternativeAdmin($id_user, $id_chat_group);
           echo $id_new_admin;
           if(isset($id_chat_group)){
            echo "ENTRO";
            $this->changeAdmin($id_user, $id_new_admin, $id_chat_group);
            $this->changeGroupCreator($id_chat_group, $id_new_admin);
           }else{ 
            //BORRAR GRUPO
            $this->deleteGroup($id_chat_group);
           }
            
        }
        $stmt = $this->conn->prepare("DELETE FROM user_partof_group WHERE id_user = ? AND id_chat_group = ?");
        $stmt->bind_param("ii", $id_user,  $id_chat_group);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as num FROM user_partof_group WHERE id_user = ? AND id_chat_group = ?");
            $stmt->bind_param("ii", $user_id,  $group_id);
            $stmt->execute();
            $deleted = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return !$deleted['num'];
        } else {
            return false;
        }
    }

    /**
     * Marcar un usuario como miembro de un grupo
     */
    public function setUserMember($id_user, $id_chat_group) {
        $stmt = $this->conn->prepare("INSERT INTO user_partof_group(id_user, id_chat_group) VALUES(?, ?)");
		$stmt->bind_param("ii", $id_user, $id_chat_group);
        $result = $stmt->execute();
        $stmt->close();
    }
    
    public function removeMembers($group_id){
        $stmt = $this->conn->prepare("DELETE FROM user_partof_group WHERE id_chat_group LiKE ? ");
		$stmt->bind_param("i", $group_id);
        $result = $stmt->execute();
        $stmt->close();
    }
    

    
    public function removeGroup($group_id){
        
        
        $stmt = $this->conn->prepare("DELETE FROM user_manage_group WHERE id_chat_group LiKE ? ");
		$stmt->bind_param("i", $group_id);
        $result = $stmt->execute();
        $stmt->close();
    }
    /**
     * Obtener usuario por usuario y contraseña
	 * return: $user
     */
    public function getUserByUsernameAndPassword($name, $password) {
  
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
        $result = mysqli_query($this->conn, "SELECT cg.id_chat_group, cg.name, cg.description, umg.id_user  
                                                FROM chat_group cg 
                                                LEFT JOIN user_manage_group umg
                                                ON umg.id_chat_group = cg.id_chat_group
                                                WHERE cg.id_chat_group IN (SELECT id_chat_group 
                                                                        FROM user_partof_group 
                                                                        WHERE id_user = $id_user)");

        while($r = mysqli_fetch_assoc($result)){
            
            $rows[] = array('data' => $r);
        }
        
        return $rows;                                            
    }
	
  
	/**
     * Obtener grupo por id usuario
	 * return: $groups
     */
    public function getContactsOfUser($id_user) {
        $result = mysqli_query($this->conn, "select ua.name, ua.id_user
 FROM  user_account ua JOIN user_contact_of_user ucou ON ucou.id_contact  = ua.id_user WHERE ucou.id_user  = $id_user;");

        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        
        return $rows;                                            
    }
    
     public function getMembers($group_id) {

        $result = mysqli_query($this->conn, "select name FROM  user_account ua 
                                                JOIN user_partof_group upog 
                                                ON upog.id_user  = ua.id_user
                                                WHERE upog.id_chat_group  = $group_id;");

        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        
        return $rows;         
        
    }
    
    
 	public function addContacts($id_user, $id_contact) {
        $result = mysqli_query($this->conn, "INSERT INTO instantM.user_contact_of_user(id_user, id_contact) VALUES($id_user, $id_contact);");                                          
    }
	/**
     * Obtener grupo por id usuario
	 * return: $groups
     */
    public function getcontactsByUserId($id_user) {
        $result = mysqli_query($this->conn, "select ua.name, ua.id_user FROM  user_account ua WHERE ua.id_user  != $id_user;");

        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        
        return $rows;                                            
    }
    /**
     * Comprueba si existe usuario
	 * return true/false
     */
    public function isUserExisted($mail, $name) {
        $consulta = $this->conn->prepare("SELECT mail from user_account WHERE mail = ? OR name = ?");
        $consulta->bind_param("ss", $mail, $name);
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

   public function getPrivateChatsOfUser($id_user) {
        $result = mysqli_query($this->conn, "SELECT DISTINCT ua.name, ua.id_user
			FROM user_account ua, private_message pm 
			WHERE (pm.id_sender_user = ua.id_user AND pm.id_receiver_user = $id_user)
	 		OR (pm.id_sender_user = $id_user AND pm.id_receiver_user = ua.id_user);");
        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }

        return $rows;
    }
        
    public function saveMessage($user_id, $message, $group_id) {
        $stmt = $this->conn->prepare("INSERT INTO group_message(message, id_user, id_chat_group) VALUES(?, ?, ?)");
		$stmt->bind_param("sii", $message, $user_id, $group_id);
        $result = $stmt->execute();
        $stmt->close();
    }
    
    public function savePrivateMessage($user_id, $message, $receiver_id) {
        $stmt = $this->conn->prepare("INSERT INTO instantM.private_message(id_sender_user, id_receiver_user, message) VALUES(?, ?, ?)");
		$stmt->bind_param("iis", $user_id, $receiver_id, $message);
        $result = $stmt->execute();
        $stmt->close();
    }
    
    public function recoverMessages($id_group) {
        $result = mysqli_query($this->conn, "SELECT gm.message, ua.name
                FROM instantM.group_message gm, instantM.user_account ua 
                WHERE ua.id_user = gm.id_user AND id_chat_group = $id_group
                ORDER BY gm.id_chat_group_message");
        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        return $rows;
    }
    
    public function recoverPrivateMessages($room_name) {
        $result = mysqli_query($this->conn, "SELECT pm.message, ua.name
                    FROM instantM.private_message pm, instantM.private_room pr, instantM.user_account ua 
                    WHERE ((pr.user1 = pm.id_receiver_user AND pr.user2 = pm.id_sender_user) OR (pr.user1 = pm.id_sender_user AND pr.user2 = pm.id_receiver_user))
                    AND pr.room_name = $room_name AND id_sender_user = ua.id_user 
                    ORDER BY pm.id_message");
        while($r = mysqli_fetch_assoc($result)){
            $rows[] = array('data' => $r);
        }
        return $rows;
    }
    
    public function getChatroom($user1, $user2) {
        $result = mysqli_query($this->conn, "SELECT room_name 
                FROM instantM.private_room
                WHERE (user1 = $user1 AND user2 = $user2)
                    OR (user1 = $user2 AND user2 = $user1)");
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    
    public function createChatroom($user1, $user2) {
        $room_name = $user1.$user2;
        $stmt = $this->conn->prepare("INSERT INTO instantM.private_room(user1, user2, room_name) VALUES(?, ?, ?)");
		$stmt->bind_param("sss", $user1, $user2, $room_name);
        $result = $stmt->execute();
        $stmt->close();
    }
}

?>
