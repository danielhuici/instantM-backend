<?php
 error_reporting(E_ALL);
 ini_set('display_errors', '1');

 require_once 'include/DB_Funciones.php';
 $db = new DB_Funciones();
  
 // json response array
 $response = array("error" => FALSE);
  
 if (isset($_POST['user_id']) && isset($_POST['group_id'])) {
  $user_id = $_POST['user_id'];
  $group_id = $_POST['group_id'];
  $isAdmin = $db->isAdmin($user_id, $group_id);
  $contact = $db->leaveGroup($user_id, $group_id); 
   $response["error"] = FALSE;
  
  
  } else {
   $response["error"] = TRUE;
   $response["error_msg"] = "¡Faltan parametros!";
  }

 echo json_encode($response); 
 
?>
