<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['like'])) {
    $tweet_id = $_POST['like'];
    $get_id = $_POST['user_id'];
    $user_id = @$_SESSION['user_id'];
    $getFromT->addlike($user_id, $tweet_id, $get_id);
}
if (isset($_POST['unlike'])) {

    $unlike = $_POST['unlike'];
    $get_id = $_POST['user_id'];
    $user_id = @$_SESSION['user_id'];
    $getFromT->dislike($user_id, $unlike, $get_id);
}
