<?php
 require_once 'include/DB_Funciones.php';
 $db = new DB_Funciones();
  
 // json response array
 $response = array("error" => FALSE);
  
 if (isset($_POST['contact_name']) && isset($_POST['username'])) {
  $contact_name = $_POST['contact_name'];
  $username = $_POST['username'];
  $contact_id = $db->getUserIdByUsername($contact_name);
  $user = $db->getUserIdByUsername($username);

  $contact = $db->deleteContact($user["id_user"], $contact_id["id_user"]);
  
   $response["error"] = FALSE;
  
  
  } else {
   $response["error"] = TRUE;
   $response["error_msg"] = "¡Faltan parametros!";
  }

 echo json_encode($response);
 
?>
