<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['comment']) && !empty($_POST['comment'])) {
    $comment = $getFromU->CheckInput($_POST['comment']);
    $user_id = @$_SESSION['user_id'];
    $tweet_id = $_POST['tweet_id'];
    $date = date('Y-m-d H:i:s');

    if (!empty($comment)) {
        $query = "INSERT INTO comments (comment,commentOn,commentBy,commentAt) VALUES (:comment,:comment_on,:comment_by,:comment_at)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':comment_on', $tweet_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_by', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_at', $date, PDO::PARAM_STR);
        $stmt->execute();
        $comments = $getFromT->comments($tweet_id);
        $tweet = $getFromT->getPopupTweet($tweet_id);

        foreach ($comments as $comment) {
            echo '<div class="tweet-show-popup-comment-box">
            <div class="tweet-show-popup-comment-inner">
                <div class="tweet-show-popup-comment-head">
                    <div class="tweet-show-popup-comment-head-left">
                         <div class="tweet-show-popup-comment-img">
                             <img src="' . BASE_URL . $comment->profileImage . '">
                         </div>
                    </div>
                    <div class="tweet-show-popup-comment-head-right">
                          <div class="tweet-show-popup-comment-name-box">
                             <div class="tweet-show-popup-comment-name-box-name"> 
                                 <a href="' . BASE_URL . $comment->username . '">' . $comment->screenName . '</a>
                             </div>
                             <div class="tweet-show-popup-comment-name-box-tname">
                                 <a href="' . BASE_URL . $comment->username . '">@' . $comment->username . ' - ' . $comment->commentAt . '</a>
                             </div>
                         </div>
                         <div class="tweet-show-popup-comment-right-tweet">
                                 <p><a href="' . BASE_URL . $tweet->username . '">@' . $tweet->username . '</a>' . $comment->comment . '</p>
                         </div>
                         <div class="tweet-show-popup-footer-menu">
                            <ul>
                                <li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
                                <li><a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>
                                ' . (($comment->commentBy === $user_id) ? '<li>
                                                <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                </a>
                                                <ul> 
                                                  <li><label class="deleteComment" data-tweet="' . $tweet->tweetID . '" data-comment="' . $comment->commentID . '" >Delete Comment</label></li>
                                                </ul>
                                                </li>' : '') . '
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--TWEET SHOW POPUP COMMENT inner END-->
            </div>
            ';
        }
    }
}
