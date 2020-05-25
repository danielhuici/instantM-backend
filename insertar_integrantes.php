<?php
 require_once 'include/DB_Funciones.php';
$db = new DB_Funciones();
  
// json response array
$response = array("error" => FALSE);
  
if (isset($_POST['id_group']) && isset($_POST['members'])) {
 $group_id = $_POST['id_group'];
 $members_string = $_POST['members'];
 $num_members = substr_count($members_string,";");
 
 $members = explode(";", $members_string, $num_members);
 $db->removeMembers($group_id);
 
 for ($i = 0; $i <= $num_members; $i++) {
    $members[$i] = str_replace(";", "", $members[$i]);
    $user = $db->getUserIdByUsername($members[$i]);
    $db->setUserMember($user["id_user"], $group_id);
 }


 /*if ($group) {
  // group stored successfully
  $response["error"] = FALSE;
  $db->setUserMember($user["id_user"], $group["id_chat_group"]);
  
 } else {
  // group failed to store
  $response["error"] = TRUE;
  $response["error_msg"] = "¡Error inesperado al crear el grupo! ";
 }
 
 */
 } else {
  $response["error"] = TRUE;
  $response["error_msg"] = "¡Faltan parametros!";
 }
 
 echo json_encode($response);

?>
