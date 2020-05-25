<?php
 require_once 'include/DB_Funciones.php';
 $db = new DB_Funciones();
 
 // json response array
 $response = array("error" => FALSE);
 
 if (isset($_POST['user_id'])) {
  $user_id = $_POST['user_id']; 
  $contacts = $db->getContactsOfUser($user_id);
  
  error_log("TEST:", $contacts);
  // contact got successfully
  $response["error"] = FALSE;
  $response["contacts"] = $contacts;
  
 } else {
  $response["error"] = TRUE;
  $response["error_msg"] = "¡Faltan parametros!";
 }
 
 echo json_encode($response);

?>
