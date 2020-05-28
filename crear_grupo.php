<!--
	Crea un grupo.
-->
<?php

	require_once 'include/DB_Funciones.php';

	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);

	if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['pic'])
	 && isset($_POST['user_id']) && isset($_POST['mode']) && isset($_POST['id'])) {
		$group_name = $_POST['name'];
		$user_id = $_POST['user_id'];
		$group_description = $_POST['description'];
		$pic_64 = $_POST['pic'];
		$group_id = $_POST['id'];
		$modo = $_POST['mode'];
		// create a new group
		$pic = base64_decode($pic_64);
		if(strcmp($modo, "WRITE") == 0){
			$group = $db->storeGroup($group_name, $user_id, $group_description, $pic); 
		}elseif(strcmp($modo, "OVERWRITE") == 0){
			$group = $db->updateGroup($group_name, $group_description, $pic, $group_id);
			$response["id"] = -1;
		}
		if ($group && strcmp($modo, "WRITE") == 0) {
			// group stored successfully
			$response["error"] = FALSE;
			$db->addUserAdmin($user_id, $group["id_chat_group"]);
			$db->setUserMember($user_id, $group["id_chat_group"]);
			$response["id"] = $group["id_chat_group"];
		} elseif(!$group) {
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
