<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_GET['ShowNotification']) && !empty($_GET['ShowNotification'])) {
    $user_id = @$_SESSION['user_id'];
    $data = $getFromM->getNotificationCount($user_id);
    echo json_encode(array('notification' => $data->totalN, 'messages' => $data->totalM));
} else {
    header('Location:' . BASE_URL . 'index.php');
}
