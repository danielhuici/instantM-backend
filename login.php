<?php
require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
  
// json response array
$response = array("error" => FALSE);
  
if (isset($_POST['mail']) && isset($_POST['password'])) {
    $mail = $_POST['mail'];
    $password = $_POST['password'];
  
    // get the user by email and password
    $user = $db->getUserByEmailAndPassword($mail, $password);
  
    if ($user != false) {
        // Usuario encontrado!
        $response["error"] = FALSE;
        $response["user"]["name"] = $user["name"];
        $response["user"]["mail"] = $user["mail"];
        
    } else {
        // Usuario no encontrado
        $response["error"] = TRUE;
        $response["error_msg"] = "Usuario o contraseña incorrectos";
        
    }
} else { // Parametros POST erroneos
    $response["error"] = TRUE;
    $response["error_msg"] = "¡Faltan parámetros!";
    
}

echo json_encode($response);