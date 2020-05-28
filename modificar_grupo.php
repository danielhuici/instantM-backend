<!--
	Modifica la información de un grupo.
-->
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);

	if (isset($_POST['name'])) {
		$group_name = $_POST['name'];
		$id_group = $_POST['id_group'];
		$description = $_POST['description'];    

		// create a new group
		$group = $db->updateGroup($group_name, $id_group, $description);
		if ($group) {
		// group stored successfully
		$response["error"] = FALSE;
		} else {
			// group failed to store
			$response["error"] = TRUE;
			$response["error_msg"] = "¡Error inesperado al modificar el grupo! ";
		}	
	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "¡Faltan parametros!";
	}

	echo json_encode($response);

?>
