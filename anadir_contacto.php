<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');


 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
  
// json response array
$response = array("error" => FALSE);
  
if (isset($_POST['user_id']) && isset($_POST['contact_id'])) {
	$user_id = $_POST['user_id'];
 	$contact_id = $_POST['contact_id'];

 $db->addContacts($user_id, $contact_id);

		// group got successfully
  $response["error"] = FALSE;

} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "¡Faltan parametros!";
}

echo json_encode($response);
 
?>
