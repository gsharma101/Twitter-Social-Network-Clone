<?php
class Message extends User
{

    protected $pdo;

    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function resentMessages($user_id)
    {
        $query = "SELECT * FROM messages LEFT JOIN users ON messageFrom = user_id WHERE messageTo = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getMessages($messageFrom, $user_id)
    {
        $query = "SELECT * FROM messages LEFT JOIN users ON messageFrom = user_id WHERE messageFrom = :messageFrom AND messageTo = :user_id OR messageTo = :messageFrom AND messageFrom = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":messageFrom", $messageFrom, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($messages as $message) {
            if ($message->messageFrom === $user_id) {
                echo ' <!-- Main message BODY RIGHT START -->
                <div class="main-msg-body-right">
                        <div class="main-msg">
                            <div class="msg-img">
                                <a href="' . BASE_URL . $message->username . '"><img src="' . BASE_URL . $message->profileImage . '"/></a>
                            </div>
                            <div class="msg">' . $message->message . '
                                <div class="msg-time">
                                ' . $this->timeAgo($message->messageOn) . '
                                </div>
                            </div>
                            <div class="msg-btn">
                                <a><i class="fa fa-ban" aria-hidden="true"></i></a>
                                <a class="deleteMsg" data-message="' . $message->messageID . '"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <!--Main message BODY RIGHT END--> ';
            } else {
                echo '<!--Main message BODY LEFT START-->
                <div class="main-msg-body-left">
                    <div class="main-msg-l">
                        <div class="msg-img-l">
                        <a href="' . BASE_URL . $message->username . '"><img src="' . BASE_URL . $message->profileImage . '"/></a>
                        </div>
                        <div class="msg-l">' . $message->message . '
                            <div class="msg-time-l">
                            ' . $this->timeAgo($message->messageOn) . '
                            </div>	
                        </div>
                        <div class="msg-btn-l">	
                            <a><i class="fa fa-ban" aria-hidden="true"></i></a>
                            <a class="deleteMsg" data-message="' . $message->messageID . '"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div> 
            <!--Main message BODY LEFT END-->';
            }
        }
    }

    public function deleteMsg($messageID, $user_id)
    {
        $query = "DELETE FROM messages WHERE messageID = :messageID 
        AND messageFrom = :user_id OR messageID = :messageID AND messageTo = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":messageID", $messageID, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getNotificationCount($user_id)
    {
        $query = "SELECT COUNT(messageID) AS totalM , (SELECT COUNT(ID) FROM notification WHERE
        notificationFor = :user_id AND status = 0) AS totalN FROM messages WHERE messageTo = :user_id
        AND status = 0 ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function notificationViewed($user_id)
    {
        $query = "UPDATE notification SET status = 1 WHERE notificationFor = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function notification($user_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM notification N 
        LEFT JOIN users U ON N.notificationFrom = U.user_id 
        LEFT JOIN tweets T ON N.target = T.tweetID
        LEFT JOIN likes L ON N.target = L.likeOn
        LEFT JOIN follow F ON N.notificationFrom = F.sender AND N.notificationFor  = F.receiver
        WHERE N.notificationFor = :user_id AND N.notificationFrom != :user_id");
        $stmt->execute(array("user_id" => $user_id));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
