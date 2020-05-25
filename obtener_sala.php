<?php
 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
$response = array("error" => FALSE);
  
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['user1']) && isset($_POST['user2'])) {
   $user1 = $_POST['user1'];
   $user2 = $_POST['user2'];
 
   $room = $db->getChatroom($user1, $user2);

   if($response == null) {
     $db->createChatroom($user1, $user2);
     $room = $db->getChatroom($user1, $user2);
   }
   
   
   $response["error"] = FALSE;
   $response["data"] = $room;
   echo json_encode($response);
   
    
} else {
    echo "ERROR";
}

?>