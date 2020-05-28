<!--
	Elimina un grupo.
-->
<?php
	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);

	if (isset($_POST['id_chat_group'])) {
		$id_chat_group = $_POST['id_chat_group'];

		$db->deleteGroup($id_chat_group);
		$response["error"] = FALSE;


	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "¡Faltan parametros!";
	}

	echo json_encode($response);

?>
