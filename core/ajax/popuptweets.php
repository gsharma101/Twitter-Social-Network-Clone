<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['showpopup'])) {
    $tweetID = $_POST['showpopup'];
    $user_id = @$_SESSION['user_id'];
    $tweet = $getFromT->getPopupTweet($tweetID);
    $user = $getFromU->userData($user_id);
    $likes = $getFromT->likes($user_id, $tweetID);
    $retweet = $getFromT->checkRetweet($tweetID, $user_id);
    $comments = $getFromT->comments($tweetID);
?>
    <div class="tweet-show-popup-wrap">
        <input type="checkbox" id="tweet-show-popup-wrap">
        <div class="wrap4">
            <label for="tweet-show-popup-wrap">
                <div class="tweet-show-popup-box-cut">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </label>
            <div class="tweet-show-popup-box">
                <div class="tweet-show-popup-inner">
                    <div class="tweet-show-popup-head">
                        <div class="tweet-show-popup-head-left">
                            <div class="tweet-show-popup-img">
                                <img src="<?php echo BASE_URL . $tweet->profileImage; ?>" />
                            </div>
                            <div class="tweet-show-popup-name">
                                <div class="t-s-p-n">
                                    <a href="<?php echo BASE_URL . $tweet->username; ?>">
                                        <?php echo $tweet->screenName; ?>
                                    </a>
                                </div>
                                <div class="t-s-p-n-b">
                                    <a href="<?php echo BASE_URL . $tweet->username; ?>">
                                        @<?php echo $tweet->username; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tweet-show-popup-head-right">
                            <button class="f-btn"><i class="fa fa-user-plus"></i> Follow </button>
                        </div>
                    </div>
                    <div class="tweet-show-popup-tweet-wrap">
                        <div class="tweet-show-popup-tweet">
                            <?php echo $getFromT->getTweetLinks($tweet->status); ?>
                        </div>
                        <div class="tweet-show-popup-tweet-ifram">
                            <?php if (!empty($tweet->tweetImage)) { ?>
                                <img src=" <?php echo BASE_URL . $tweet->tweetImage; ?>" />
                            <?php } ?>
                        </div>
                    </div>
                    <div class="tweet-show-popup-footer-wrap">
                        <div class="tweet-show-popup-retweet-like">
                            <div class="tweet-show-popup-retweet-left">
                                <div class="tweet-retweet-count-wrap">
                                    <div class="tweet-retweet-count-head">
                                        RETWEET
                                    </div>
                                    <div class="tweet-retweet-count-body">
                                        <?php echo $tweet->retweetCount; ?>
                                    </div>
                                </div>
                                <div class="tweet-like-count-wrap">
                                    <div class="tweet-like-count-head">
                                        LIKES
                                    </div>
                                    <div class="tweet-like-count-body">
                                        <?php echo $tweet->likesCount; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tweet-show-popup-retweet-right">

                            </div>
                        </div>
                        <div class="tweet-show-popup-time">
                            <span> <?php echo $getFromT->TimeAgo($tweet->postedOn); ?></span>
                        </div>
                        <div class="tweet-show-popup-footer-menu">
                            <ul>
                                <?php if ($getFromU->loggedIn() === true) {
                                    echo '<li><button><a href="#"><i class="fa fa-share" aria-hidden="true"></i></a></button></li>	
                                <li>' . (($tweet->tweetID === $retweet['retweetID']) ? '<button class="retweeted" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i></a><span class="retweetCounter">' . $tweet->retweetCount . '</span></button>' :
                                        '<button class="retweet" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i></a><span class="retweetCounter">' . (($tweet->retweetCount > 0) ? $tweet->retweetCount : '') . '</span></button>') . '</li>
                                <li>' . (($likes['likeOn'] === $tweet->tweetID) ? '<button class="unlike-btn" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-heart" aria-hidden="true"></i></a><span class="likesCounter">' . $tweet->likesCount . '</span></button>' :
                                        '<button class="like-btn" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a><span class="likesCounter">' . (($tweet->likesCount > 0) ? $tweet->likesCount : '') . '</span></button>') . '</li>
                                <li>' . (($tweet->tweetBy === $user_id) ? '
                                    <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
                                    <ul> 
                                      <li><label data-tweet="' . $tweet->tweetID . '" class="deleteTweet">Delete Tweet</label></li>
                                    </ul>' : '') . '</li>';
                                } else {
                                ?>
                                    <li><button type="buttton"><i class="fa fa-share" aria-hidden="true"></i></button></li>
                                    <li><button type="button"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">RETWEET-COUNT</span></button></li>
                                    <li><button type="button"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCount">LIKES-COUNT</span></button></button></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--tweet-show-popup-inner end-->
                <?php if ($getFromU->loggedIn() === true) { ?>
                    <div class="tweet-show-popup-footer-input-wrap">
                        <div class="tweet-show-popup-footer-input-inner">
                            <div class="tweet-show-popup-footer-input-left">
                                <img src="<?php echo BASE_URL . $user->profileImage; ?>" />
                            </div>
                            <div class="tweet-show-popup-footer-input-right">
                                <input id="commentField" type="text" data-tweet="<?php echo $tweet->tweetID; ?>" name="comment" placeholder="Reply to <?php echo $tweet->username; ?>">
                            </div>
                        </div>
                        <div class="tweet-footer">
                            <div class="t-fo-left">
                                <ul>
                                    <li>
                                        <!-- <label for="t-show-file"><i class="fa fa-camera" aria-hidden="true"></i></label>
		 				<input type="file" id="t-show-file"> -->
                                    </li>
                                    <li class="error-li">
                                    </li>
                                </ul>
                            </div>
                            <div class="t-fo-right">
                                <input type="submit" id="postComment">
                                <script type="text/javascript" src="<?php echo BASE_URL; ?>comment.js"></script>
                                <script type="text/javascript" src="<?php echo BASE_URL; ?>follow.js"></script>
                            </div>
                        </div>
                    </div>
                    <!--tweet-show-popup-footer-input-wrap end-->
                <?php } ?>
                <div class="tweet-show-popup-comment-wrap">
                    <div id="comments">
                        <?php
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
                                                 <a href="' . BASE_URL . $comment->username . '">@' . $comment->username . ' - ' . $getFromT->TimeAgo($comment->commentAt) . '</a>
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
                        ?>
                    </div>
                </div>
                <!--tweet-show-popup-box ends-->
            </div>
        </div>

    <?php
}
    ?>