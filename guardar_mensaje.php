<?php
 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
$response = array("test" => "nothing");
  
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['id_user']) && isset($_POST['message']) && isset($_POST['id_group'])) {
   $user_id = $_POST['id_user'];
   $message = $_POST['message'];
   $group_id = $_POST['id_group'];
 
   $db->saveMessage($user_id, $message, $group_id);

   echo json_encode($response);
    
} else {
    echo "ERROR";
}

?>