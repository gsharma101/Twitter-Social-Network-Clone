<?php
include_once('core/init.php');
$user_id = @$_SESSION['user_id'];
$user = $getFromU->userData($user_id);
$notify = $getFromM->getNotificationCount($user_id);
if ($getFromU->loggedIn() === false) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['submit'])) {
    $currentPwd = $getFromU->CheckInput($_POST['currentPwd']);
    $newPassword = $getFromU->CheckInput($_POST['newPassword']);
    $rePassword = $getFromU->CheckInput($_POST['rePassword']);
    $error = array();

    if (!empty($currentPwd) && !empty($newPassword) && !empty($newPassword)) {
        $passwordCheck = password_verify($currentPwd, $user->user_password);
        if ($passwordCheck == false) {
            $error['newPassword'] = "Wrong password";
        } else {
            if (strlen($newPassword) < 6) {
                $error['newPassword'] = "New password to short";
            } else {
                $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $query = "UPDATE users SET user_password=:user_password WHERE user_id=:user_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':user_password', $hashPassword, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                header('Location:' . BASE_URL . $user->username);
                exit();
            }
        }
    } else {
        $error['fields'] = "All fields ar required";
    }
}
?>

<head>
    <title>Password setting</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" />
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style-complete.css" />
</head>
<!--Helvetica Neue-->

<body>
    <div class="wrapper">
        <!-- header wrapper -->
        <div class="header-wrapper">

            <div class="nav-container">
                <!-- Nav -->
                <div class="nav">

                    <div class="nav-left">
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>home.php"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
                            <li><a href="i/notifications"><i class="fa fa-bell" aria-hidden="true"></i>Notification<span id="notification">
                                        <?php
                                        if ($notify->totalN > 0) {
                                            echo '<span class="span-i">' . $notify->totalN . '</span>';
                                        }
                                        ?>
                                    </span></a>
                            </li>
                            <li id="messagePopup"><i class="fa fa-envelope" aria-hidden="true"></i>Messages<span id="messages">
                                    <?php
                                    if ($notify->totalM > 0) {
                                        echo '<span class="span-i">' . $notify->totalM . '</span>';
                                    }
                                    ?>
                                </span></li>
                        </ul>
                    </div><!-- nav left ends-->

                    <div class="nav-right">
                        <ul>
                            <li>
                                <input type="text" placeholder="Search" class="search" />
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <div class="search-result">
                                </div>
                            </li>

                            <li class="hover"><label class="drop-label" for="drop-wrap1"><img src="<?php echo BASE_URL . $user->profileImage; ?>" /></label>
                                <input type="checkbox" id="drop-wrap1">
                                <div class="drop-wrap">
                                    <div class="drop-inner">
                                        <ul>
                                            <li><a href="<?php echo $user->username; ?>"><?php echo $user->username; ?></a>
                                            </li>
                                            <li><a href="<?php echo BASE_URL; ?>settings/account">Settings</a></li>
                                            <li><a href="<?php echo BASE_URL; ?>includes/logout.php">Log out</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li><label class="addTweetBtn">Tweet</label></li>
                        </ul>
                    </div><!-- nav right ends-->

                </div><!-- nav ends -->
                <script type="text/javascript" src="assets/js/messages.js"></script>
                <script type="text/javascript" src="assets/js/like.js"></script>
                <script type="text/javascript" src="assets/js/retweet.js"></script>
                <script type="text/javascript" src="assets/js/popuptweets.js"></script>
                <script type="text/javascript" src="assets/js/comment.js"></script>
                <script type="text/javascript" src="assets/js/delete.js"></script>
                <script type="text/javascript" src="assets/js/popupForm.js"></script>
                <script type="text/javascript" src="assets/js/fetch.js"></script>
                <script type="text/javascript" src="assets/js/postMessage.js"></script>
                <script type="text/javascript" src="assets/js/notification.js"></script>
                <script type="text/javascript" src="assets/js/search.js"></script>
                <script type="text/javascript" src="assets/js/hashtags.js"></script>
                <script type="text/javascript" src="assets/js/follow.js"></script>

            </div><!-- nav container ends -->

        </div><!-- header wrapper end -->
        <div class="container-wrap">

            <div class="lefter">
                <div class="inner-lefter">

                    <div class="acc-info-wrap">
                        <div class="acc-info-bg">
                            <!-- PROFILE-COVER -->
                            <img src="<?php echo BASE_URL . $user->profileCover; ?>" />
                        </div>
                        <div class="acc-info-img">
                            <!-- PROFILE-IMAGE -->
                            <img src="<?php echo  BASE_URL . $user->profileImage; ?>" />
                        </div>
                        <div class="acc-info-name">
                            <h3><?php echo $user->screenName; ?></h3>
                            <span><a href="<?php echo BASE_URL . $user->username; ?>"><?php echo $user->username; ?></a></span>
                        </div>
                    </div>
                    <!--Acc info wrap end-->

                    <div class="option-box">
                        <ul>
                            <li>
                                <a href="account" class="bold">
                                    <div>
                                        Account
                                        <span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="password">
                                    <div>
                                        Password
                                        <span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <!--LEFTER ENDS-->

            <div class="righter">
                <div class="inner-righter">
                    <div class="acc">
                        <div class="acc-heading">
                            <h2>Password</h2>
                            <h3>Change your password or recover your current one.</h3>
                        </div>
                        <form method="POST" action="#">
                            <div class="acc-content">
                                <div class="acc-wrap">
                                    <div class="acc-left">
                                        Current password
                                    </div>
                                    <div class="acc-right">
                                        <input type="password" name="currentPwd" />
                                        <span>
                                            <?php
                                            if (isset($error['currentPwd'])) {
                                                echo $error['currentPwd'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="acc-wrap">
                                    <div class="acc-left">
                                        New password
                                    </div>
                                    <div class="acc-right">
                                        <input type="password" name="newPassword" />
                                        <span>
                                            <?php
                                            if (isset($error['newPassword'])) {
                                                echo $error['newPassword'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="acc-wrap">
                                    <div class="acc-left">
                                        Verify password
                                    </div>
                                    <div class="acc-right">
                                        <input type="password" name="rePassword" />
                                        <span>
                                            <?php
                                            if (isset($error['rePassword'])) {
                                                echo $error['rePassword'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="acc-wrap">
                                    <div class="acc-left">
                                    </div>
                                    <div class="acc-right">
                                        <input type="Submit" name="submit" value="Save changes" />
                                    </div>
                                    <div class="settings-error">
                                        <?php
                                        if (isset($error['fields'])) {
                                            echo $error['fields'];
                                        }
                                        ?>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
                <div class="content-setting">
                    <div class="content-heading">

                    </div>
                    <div class="content-content">
                        <div class="content-left">

                        </div>
                        <div class="content-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--RIGHTER ENDS-->
    </div>
    <!--CONTAINER_WRAP ENDS-->
    </div>
    <!-- ends wrapper -->
</body>

</html>