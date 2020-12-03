<?php
include_once('core/init.php');
$user_id = @$_SESSION['user_id'];
$user = $getFromU->userData($user_id);
$notify = $getFromM->getNotificationCount($user_id);

if ($getFromU->loggedIn() === false) {
    header('Locatiom:' . BASE_URL . 'index.php');
    exit();
}
if (isset($_POST['submit'])) {
    $username = $getFromU->CheckInput($_POST['username']);
    $email = $getFromU->CheckInput($_POST['email']);
    $error = array();

    if (!empty($email) && !empty($username)) {
        if ($user->username != $username && $getFromU->checkUsername($username) === true) {
            $error['username'] = "The username is not available";
        } elseif (preg_match("/[^a-zA-Z0-9\!]", $username)) {
            $error['username'] = "Only characters and numbers allowed";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = "Invalid email";
        } elseif ($user->email != $email && $getFromU->checkEmail($email) === true) {
            $error['email'] = "Email already in use";
        } else {
            $query = "UPDATE users SET username=:username,email=:email WHERE user_id=:user_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            header('Location:' . BASE_URL . 'settings/account');
            exit();
        }
    } else {
        $error['fields'] = "All fields are required";
    }
}
?>
<html>

<head>
    <title>Account settings page</title>
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
                            <li><a href="home.php"><i class="fa fa-home" aria-hidden="true"></i>Home</a></li>
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
                                            <li><a href="<?php echo BASE_URL . $user->username; ?>"><?php echo $user->username; ?></a>
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
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/messages.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/like.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/retweet.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/popuptweets.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/comment.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/delete.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/popupForm.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/fetch.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/postMessage.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/notification.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/search.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/hashtags.js"></script>
                <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/follow.js"></script>
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
                            <h2>Account</h2>
                            <h3>Change your basic account settings.</h3>
                        </div>
                        <div class="acc-content">
                            <form method="POST" action="#">
                                <div class="acc-wrap">
                                    <div class="acc-left">
                                        USERNAME
                                    </div>
                                    <div class="acc-right">
                                        <input type="text" name="username" value="<?php echo $user->username; ?>">
                                        <span>
                                            <?php
                                            if (isset($error['username'])) {
                                                echo $error['username'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="acc-wrap">
                                    <div class="acc-left">
                                        Email
                                    </div>
                                    <div class="acc-right">
                                        <input type="text" name="email" value="<?php echo $user->email; ?>">
                                        <span>
                                            <?php
                                            if (isset($error['email'])) {
                                                echo $error['email'];
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

    </div><!-- ends wrapper -->
</body>

</html>