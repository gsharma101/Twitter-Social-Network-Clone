<?php
include_once("../init.php");
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['deleteComment']) && !empty($_POST['deleteComment'])) {
    $user_id = @$_SESSION['user_id'];
    $commentID = $_POST['deleteComment'];
    $query = "DELETE FROM comments WHERE commentID=:comment_id AND commentBy=:comment_by";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":comment_id", $commentID, PDO::PARAM_INT);
    $stmt->bindParam(":comment_by", $user_id, PDO::PARAM_INT);
    $stmt->execute();
}
