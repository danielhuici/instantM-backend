<?php
 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
$response = array("test" => "nothing");
  
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['id_group'])) {
   $group_id = $_POST['id_group'];
 
   $messages = $db->recoverMessages($group_id);
   
   $response["error"] = FALSE;
   $response["messages"] = $messages;
   echo json_encode($response);
    
} else {
    $response["error"] = TRUE;
    echo json_encode($response);
}

?>