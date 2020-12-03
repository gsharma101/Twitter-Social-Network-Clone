<?php
include_once('../init.php');
$getFromU->preventAccess($_SERVER['REQUEST_METHOD'], realpath(__FILE__), realpath($_SERVER['SCRIPT_FILENAME']));
if (isset($_POST['hashtag'])) {
    $hashtag = $getFromU->CheckInput($_POST['hashtag']);
    $mention = $getFromU->CheckInput($_POST['hashtag']);

    if (substr($hashtag, 0, 1) === '#') {
        $trend = str_replace('#', '', $hashtag);
        $trends = $getFromT->getTrendByHash($trend);

        foreach ($trends as $hashtag) {
            echo '<li><a href="#"><span class="getValue">' . $hashtag->hashtag . '</span></a></li>';
        }
    }

    if (substr($mention, 0, 1) === '@') {
        $mention = str_replace('@', '', $mention);
        $mentions = $getFromT->getMention($mention);

        foreach ($mentions as $mention) {
            echo '<li><div class="nav-right-down-inner">
                <div class="nav-right-down-left">
                    <span><img src="' . BASE_URL . $mention->profileImage . '"></span>
                </div>
                <div class="nav-right-down-right">
                    <div class="nav-right-down-right-headline">
                        <a>' . $mention->screenName . '</a><span class="getValue">' . $mention->username . '</span>
                    </div>
                </div>
            </div><!--nav-right-down-inner end-here-->
            </li>';
        }
    }
}
