<?php
class Follow extends User
{

    protected $pdo;

    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function checkFollow($followerID, $user_id)
    {
        $query = "SELECT * FROM follow WHERE sender = :user_id AND receiver = :followerID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':followerID', $followerID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function followBtn($profileID, $user_id, $followID)
    {
        $data = $this->checkFollow($profileID, $user_id);
        if ($this->loggedIn() === true) {
            if ($profileID != $user_id) {
                if ($data['receiver'] == $profileID) {
                    return "<button class='f-btn following-btn follow-btn' data-follow='" . $profileID . "' data-profile='" . $followID . "'>Following</button>";
                } else {
                    return "<button class='f-btn follow-btn' data-follow='" . $profileID . "' data-profile='" . $followID . "' ><i class='fa fa-user-plus'></i>&nbsp;Follow</button>";
                }
            } else {
                return "<button class='f-btn' onclick=location.href='profileEdit.php'><i class='fa fa-tools'></i>Edit Profile</button>";
            }
        } else {
            return "<button class='f-btn' onclick=location.href='index.php'><i class='fa fa-user-plus'></i>Follow</button>";
        }
    }

    public function follow($followID, $user_id, $profileID)
    {
        $this->create('follow', array('sender' => $user_id, 'receiver' => $followID, 'followOn' => date("Y-m-d h:i:s")));
        $this->addFollowCount($followID, $user_id);
        $stmt = $this->pdo->prepare('SELECT user_id , following , followers  FROM  users LEFT JOIN follow ON sender = :user_id  AND CASE WHEN receiver = :user_id THEN sender = :user_id END WHERE user_id = :profileID');
        $stmt->execute(array('user_id' => $user_id, 'profileID' => $profileID));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data);
        $this->sendNotification($followID, $user_id, $followID, 'follow');
    }

    public function unfollow($followID, $user_id, $profileID)
    {
        $this->delete('follow', array('sender' => $user_id, 'receiver' => $followID));
        $this->removeFollowCount($followID, $user_id);
        $stmt = $this->pdo->prepare('SELECT user_id , following , followers  FROM users LEFT JOIN follow ON sender = :user_id  AND CASE WHEN receiver =:user_id THEN sender = :user_id END WHERE user_id = :profileID');
        $stmt->execute(array('user_id' => $user_id, 'profileID' => $profileID));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }

    public function addFollowCount($followID, $user_id)
    {
        $query = "UPDATE users SET following = following + 1 where user_id = :user_id; UPDATE users SET followers  = followers + 1 WHERE user_id = :followID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array("user_id" => $user_id, "followID" => $followID));
    }
    public function removeFollowCount($followID, $user_id)
    {
        $query = "UPDATE users SET following = following - 1 where user_id = :user_id; UPDATE users SET followers = followers - 1 WHERE user_id = :followID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array("user_id" => $user_id, "followID" => $followID));
    }

    public function followersList($profileID, $user_id, $followID)
    {
        $query = "SELECT * FROM users LEFT JOIN follow ON 
        sender = user_id AND CASE WHEN receiver = :user_id THEN sender = user_id END WHERE receiver is NOT  NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $profileID, PDO::PARAM_INT);
        $stmt->execute();
        $followings = $stmt->fetchALL(PDO::FETCH_OBJ);

        foreach ($followings as $following) {
            echo '<div class="follow-unfollow-box">
            <div class="follow-unfollow-inner">
                <div class="follow-background">
                    <img src="' . BASE_URL . $following->profileCover . '"/>
                </div>
                <div class="follow-person-button-img">
                    <div class="follow-person-img"> 
                         <img src="' . BASE_URL . $following->profileImage . '"/>
                    </div>
                    <div class="follow-person-button">
                         ' . $this->followBtn($following->user_id, $user_id, $followID) . '
                    </div>
                </div>
                <div class="follow-person-bio">
                    <div class="follow-person-name">
                        <a href="' . BASE_URL . $following->username . '">' . $following->screenName . '</a>
                    </div>
                    <div class="follow-person-tname">
                        <a href="' . BASE_URL . $following->username . '">' . $following->username . '</a>
                    </div>
                    <div class="follow-person-dis">
                    ' . Tweet::getTweetLinks($following->bio) . '
                    </div>
                </div>
            </div>
        </div>';
        }
    }

    public function followingList($profileID, $user_id, $followID)
    {
        $query = "SELECT * FROM users LEFT JOIN follow ON 
        receiver = user_id AND CASE WHEN sender = :user_id THEN receiver = user_id END WHERE sender is NOT  NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $profileID, PDO::PARAM_INT);
        $stmt->execute();
        $followings = $stmt->fetchALL(PDO::FETCH_OBJ);

        foreach ($followings as $following) {
            echo '<div class="follow-unfollow-box">
            <div class="follow-unfollow-inner">
                <div class="follow-background">
                    <img src="' . BASE_URL . $following->profileCover . '"/>
                </div>
                <div class="follow-person-button-img">
                    <div class="follow-person-img"> 
                         <img src="' . BASE_URL . $following->profileImage . '"/>
                    </div>
                    <div class="follow-person-button">
                         ' . $this->followBtn($following->user_id, $user_id, $followID) . '
                    </div>
                </div>
                <div class="follow-person-bio">
                    <div class="follow-person-name">
                        <a href="' . BASE_URL . $following->username . '">' . $following->screenName . '</a>
                    </div>
                    <div class="follow-person-tname">
                        <a href="' . BASE_URL . $following->username . '">' . $following->username . '</a>
                    </div>
                    <div class="follow-person-dis">
                    ' . Tweet::getTweetLinks($following->bio) . '
                    </div>
                </div>
            </div>
        </div>';
        }
    }

    public function whoToFollow($user_id, $profileID)
    {
        $query = "SELECT * FROM users where user_id != :user_id AND user_id NOT IN (SELECT receiver FROM follow WHERE sender = :user_id ) ORDER BY rand() LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchALL(PDO::FETCH_OBJ);

        echo '<div class="follow-wrap"><div class="follow-inner"><div class="follow-title"><h3>Who to follow</h3></div>';
        foreach ($data as $user) {
            echo '<div class="follow-body">
            <div class="follow-img">
              <img src="' . BASE_URL . $user->profileImage . '"/>
            </div>
            <div class="follow-content">
                <div class="fo-co-head">
                    <a href="' . BASE_URL . $user->username . '">' . $user->screenName . '</a><span>@' . $user->username . '</span>
                </div>
                ' . $this->followBtn($user->user_id, $user_id, $profileID) . '
            </div>
        </div>';
        }
        echo '</div>
        </div>';
    }
}
