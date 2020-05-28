<!--
	Obtiene los grupos de un usuario.
-->
<?php
	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);

	if (isset($_POST['user_id'])) {
		$user_id = $_POST['user_id'];
		$groups = $db->getGroupsByUserId($user_id);

		// group got successfully
		$response["error"] = FALSE;
		$response["groups"] = $groups;

	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "¡Faltan parametros!";
	}

	echo json_encode($response);

?>
