<?php
include_once("../init.php");
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST) && !empty(isset($_POST))) {

	$status  = $getFromU->CheckInput($_POST['status']);
	$user_id = @$_SESSION['user_id'];
	$tweetImage = '';

	if (!empty($status) || !empty($_FILES['file']['name'][0])) {
		if (!empty($_FILES['file']['name'][0])) {
			$tweetImage = $getFromU->uploadImage($_FILES['file']);
		}
		if (strlen($status) > 140) {
			$error = "The text of your tweet is to long";
		}
		$Tweet_id = $getFromU->create('tweets', array('status' => $status, 'tweetBy' => $user_id, 'tweetImage' => $tweetImage, 'postedOn' => date('Y-m-d H:i:s')));

		preg_match_all("/#+([a-zA-Z0-9_]+)/i", $status, $hashtag);
		if (!empty($hashtag)) {
			$getFromT->addTrend($status);
		}
		$getFromT->addMention($status, $user_id, $Tweet_id);
		$result['success'] = "Your tweet has been posted";
		echo json_encode($result);
		header("Location: home.php");
		exit();
	} else {
		$error = "Type or choose an image";
	}
	if (isset($error)) {
		$result['error'] = $error;
		echo json_encode($result);
	}
}
