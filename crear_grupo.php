<?php
 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
  
// json response array
$response = array("error" => FALSE);
  
if (isset($_POST['name']) && isset($_POST['username'])) {
    $group_name = $_POST['name'];
	$username = $_POST['username'];
    
	 $user = $db->getUserIdByUsername($username);
	
	// create a new group
	$group = $db->storeGroup($group_name, $user["id_user"]);
	if ($group) {
		// group stored successfully
		$response["error"] = FALSE;
	} else {
		// group failed to store
		$response["error"] = TRUE;
		$response["error_msg"] = "¡Error inesperado al crear el grupo! ";
	}
    
	
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "¡Faltan parametros!";
}

echo json_encode($response);
 
?>