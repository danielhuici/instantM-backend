<!--
	Actualiza la información de perfil.
-->
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();

	// json response array
	$response = array("error" => FALSE);

	if (isset($_POST['name']) && isset($_POST['birthday'])
		&& isset($_POST['mail']) && isset($_POST['id_user'])
		&& isset($_POST['state'])) {

		$name = $_POST['name'];
		$mail = $_POST['mail'];
		$state = $_POST['state'];
		$birthday = $_POST['birthday'];
		$id_user = $_POST['id_user'];

		// create a new user
		$user = $db->updateUser( $name, $mail, $state, $birthday, $id_user);
		if(isset($_POST['password'])){
			$new_password = $_POST['password'];
			$user = $db->updatePassword($new_password, $id_user);
		}
		if ($user) {
			// user stored successfully
			$response["error"] = FALSE;

		} else {
			// user failed to store
			$response["error"] = TRUE;
			$response["error_msg"] = "¡Error inesperado!";
		}
	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "¡Faltan parametros!";
	}

	echo json_encode($response);

?>
