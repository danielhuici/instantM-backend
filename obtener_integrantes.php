<!--
	obtiene los integrantes de un grupo.
-->
<?php
	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);


	if (isset($_POST['group_id'])) {
		$group_id = $_POST['group_id'];

		$members = $db->getMembers($group_id);

		// group got successfully
		$response["error"] = FALSE;
		$response["members"] = $members;

	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "Faltan parametros";
	}

	echo json_encode($response);

?>
