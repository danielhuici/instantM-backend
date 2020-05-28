<!--
	Guarda un mensaje privado.
-->
<?php
	require_once 'include/DB_Funciones.php';
	$db = new DB_Funciones();
	$response = array("test" => "nothing");

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if (isset($_POST['id_user']) && isset($_POST['message']) && isset($_POST['id_receiver'])) {
		$user_id = $_POST['id_user'];
		$message = $_POST['message'];
		$id_receiver = $_POST['id_receiver'];

		$db->savePrivateMessage($user_id, $message, $id_receiver);

		echo json_encode($response);

	} else {
		echo "ERROR";
	}

?>
