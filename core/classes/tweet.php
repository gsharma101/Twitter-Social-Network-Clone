<?php
class Tweet extends User
{

	protected $pdo;

	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function tweets($user_id, $num)
	{
		$query = "SELECT * FROM tweets LEFT JOIN users ON tweetBy = user_id 
		WHERE tweetBy =:user_id AND retweetID = 0 OR tweetBy = user_id AND retweetBy != :user_id AND tweetBy IN(SELECT receiver FROM follow where sender =:user_id) ORDER BY tweetID DESC LIMIT :num";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':num', $num, PDO::PARAM_INT);
		$stmt->execute();
		$tweets  = $stmt->fetchAll(PDO::FETCH_OBJ);
		foreach ($tweets as $tweet) {
			$likes = $this->likes($user_id, $tweet->tweetID);
			$retweet = $this->checkRetweet($tweet->tweetID, $user_id);
			$user = $this->userData($tweet->retweetBy);
			echo '<div class="all-tweet">
<div class="t-show-wrap">	
 <div class="t-show-inner">
 ' . (($retweet['retweetID'] === $tweet->retweetID or $tweet->retweetID > 0) ? '
    <div class="t-show-banner">
		<div class="t-show-banner-inner">
			<span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>' . $user->screenName . ' Retweeted</span>
		</div>
	</div>
    ' : '') . '
    ' . ((!empty($tweet->retweetMsg) && $tweet->tweetID === $retweet['retweetID'] or $tweet->retweetID > 0) ?
				'<div class="t-show-popup" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '">
       <div class="t-show-head">
				<div class="t-show-img">
					<img src="' . BASE_URL . $user->profileImage . '"/>
				</div>
				<div class="t-s-head-content">
					<div class="t-h-c-name">
						<span><a href="' . BASE_URL . $user->screenName . '">' . $user->screenName . '</a></span>
						<span>@' . $user->username . '</span>
						<span>' . $retweet['postedOn'] . '</span>
					</div>
					<div class="t-h-c-dis">
						' . $this->getTweetLinks($tweet->retweetMsg) . '
					</div>
				</div>
			</div>
			<div class="t-s-b-inner">
				<div class="t-s-b-inner-in">
					<div class="retweet-t-s-b-inner">
						' . ((!empty($tweet->tweetImage)) ? '
						<div class="retweet-t-s-b-inner-left">
							<img src="' . BASE_URL . $tweet->tweetImage . '" class="imagePopup" data-tweet="' . $tweet->tweetID . '">	
						</div>' : '') . '
						<div">
							<div class="t-h-c-name">
								<span><a href="' . BASE_URL . $tweet->screenName . '">' . $tweet->screenName . '</a></span>
								<span>@' . $tweet->username . '</span>
								<span>' . $this->TimeAgo($tweet->postedOn) . '</span>
							</div>
							<div class="retweet-t-s-b-inner-right-text">		
								' . $this->getTweetLinks($tweet->status) . '
							</div>
						</div>
					</div>
				</div>
			</div>
            </div>' : '
	<div class="t-show-popup" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '">
		<div class="t-show-head">
			<div class="t-show-img">
				<img src="' . $tweet->profileImage . '"/>
			</div>
			<div class="t-s-head-content">
				<div class="t-h-c-name">
					<span><a href="' . $tweet->username . '">' . $tweet->screenName . '</a></span>
					<span>@' . $tweet->username . '</span>
					<span>' . $this->TimeAgo($tweet->postedOn) . '</span>
				</div>
				<div class="t-h-c-dis">
					' . $this->getTweetLinks($tweet->status) . '
				</div>
			</div>
		</div>' .
				((!empty($tweet->tweetImage)) ? '
            <!--tweet show head end-->
		<div class="t-show-body">
		  <div class="t-s-b-inner">
		   <div class="t-s-b-inner-in">
		     <img src="' . $tweet->tweetImage . '" class="imagePopup" data-tweet="' . $tweet->tweetID . '">
		   </div>
		  </div>
		</div>
		<!--tweet show body end-->
        ' : '') . '
 </div>') . '
	<div class="t-show-footer">
		<div class="t-s-f-right">
			<ul> 
				<li><button><a href="#"><i class="fa fa-share" aria-hidden="true"></i></a></button></li>	
                                <li>' . (($tweet->tweetID === $retweet['retweetID']) ? '<button class="retweeted" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i></a><span class="retweetCounter">' . $tweet->retweetCount . '</span></button>' :
				'<button class="retweet" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i></a><span class="retweetCounter">' . (($tweet->retweetCount > 0) ? $tweet->retweetCount : '') . '</span></button>') . '</li>
                                <li>' . (($likes['likeOn'] === $tweet->tweetID) ? '<button class="unlike-btn" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-heart" aria-hidden="true"></i></a><span class="likesCounter">' . $tweet->likesCount . '</span></button>' :
				'<button class="like-btn" data-tweet="' . $tweet->tweetID . '" data-user="' . $tweet->tweetBy . '"><a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a><span class="likesCounter">' . (($tweet->likesCount > 0) ? $tweet->likesCount : '') . '</span></button>') . '</li>
                                ' . (($tweet->tweetBy === $user_id) ? ' <li>
                                    <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
                                    <ul> 
                                      <li><label data-tweet="' . $tweet->tweetID . '" class="deleteTweet">Delete Tweet</label></li>
                                    </ul>
                                </li>' : '') . '
                            </ul>
		</div>
	</div>
</div>
</div>
</div>';
		}
	}

	public function comments($tweet_id)
	{
		$query = "SELECT * FROM comments LEFT JOIN users ON commentBy=user_id WHERE commentOn=:tweet_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getTrendByHash($hashtag)
	{
		$query = "SELECT * FROM  trends WHERE hashtag LIKE :hashtag LIMIT 5";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':hashtag', $hashtag . '%');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getUserTweets($user_id)
	{
		$query = "SELECT * FROM tweets LEFT JOIN users ON tweetBy = user_id WHERE tweetBy = :user_id AND retweetID= 0 OR retweetBy = :user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getMention($mention)
	{
		$query = "SELECT user_id , username , screenName , profileImage , profileCover 
		FROM users WHERE username LIKE :mention OR screenName LIKE :mention LIMIT 5";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(':mention', $mention . '%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function addTrend($hashtags)
	{
		preg_match_all("/#+([a-zA-Z0-9_]+)/i", $hashtags, $matches);

		if ($matches) {
			$result = array_values($matches[1]);
		}
		$sql = "INSERT INTO trends (hashtag,createdOn) VALUES (:hashtags, CURRENT_TIMESTAMP)";
		foreach ($result as $trends) {
			if ($stmt = $this->pdo->prepare($sql)) {
				$stmt->execute(array(':hashtags' => $trends));
			}
		}
	}

	public function addMention($status, $user_id, $tweet_id)
	{
		preg_match_all("/@+([a-zA-Z0-9_]+)/i", $status, $matches);

		if ($matches) {
			$result = array_values($matches[1]);
		}
		$sql = "SELECT * FROM users WHERE username = :mention";
		foreach ($result as $trends) {
			if ($stmt = $this->pdo->prepare($sql)) {
				$stmt->execute(array(':mention' => $trends));
				$data = $stmt->fetch(PDO::FETCH_OBJ);
			}
		}
		if (!$data->user_id != $user_id) {
			$this->sendNotification($data->user_id, $user_id, $tweet_id, 'mention');
		}
	}

	public function getTweetLinks($tweet)
	{
		$tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/", "<a href='$0' target='_blank'>$0</a>", $tweet);
		$tweet = preg_replace("/#([\w]+)/", "<a href='" . BASE_URL . "hashtag/$1'>$0</a>", $tweet);
		$tweet = preg_replace("/@([\w]+)/", "<a href='" . BASE_URL . "$1'>$0</a>", $tweet);
		return $tweet;
	}

	public function getPopupTweet($tweet_id)
	{
		$query = "SELECT * FROM tweets , users WHERE tweetID=:tweet_id AND tweetBy=user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function countTweets($user_id)
	{
		$query = "SELECT COUNT(tweetID) AS totalTweets FROM tweets 
        WHERE tweetBy = :user_id AND retweetID = 0 OR retweetBy = :user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count =  $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalTweets;
	}

	public function likesCount($user_id)
	{
		$query = "SELECT COUNT(likeID) AS totalLikes FROM likes WHERE likeBy = :user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$count =  $stmt->fetch(PDO::FETCH_OBJ);
		echo $count->totalLikes;
	}

	public function retweet($tweet_id, $user_id, $get_id, $comment)
	{
		$stmt = $this->pdo->prepare("UPDATE tweets SET retweetCount = retweetCount +1 WHERE tweetID=:tweet_id");
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();

		$query = "INSERT INTO tweets (status,tweetBy,tweetImage,retweetID, retweetBy,postedOn,likesCount,retweetCount,retweetMsg ) SELECT 
		status,tweetBy,tweetImage, tweetID,:user_id,postedOn,likesCount,retweetCount,:retweetMsg FROM tweets WHERE tweetID=:tweet_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':retweetMsg', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		$this->sendNotification($get_id, $user_id, $tweet_id, 'retweet');
	}

	public function checkRetweet($tweet_id, $user_id)
	{
		$query = "SELECT * FROM tweets WHERE retweetID=:tweet_id AND retweetBy =:user_id OR tweetID=:tweet_id retweetBy=:user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->bindParam(':user_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function likes($user_id, $tweet_id)
	{
		$query = "SELECT * FROM likes  WHERE likeBy = :user_id AND likeOn=:tweet_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function addlike($user_id, $tweet_id, $get_id)
	{
		$query = "UPDATE tweets SET likesCount = likesCount +1 WHERE tweetID=:tweet_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		//inserting in likes
		$Sqlquery = "INSERT INTO likes (likeBy,likeOn) VALUES (:like_by,:like_on)";
		$stmt = $this->pdo->prepare($Sqlquery);
		$stmt->bindParam(':like_by', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':like_on', $tweet_id, PDO::PARAM_INT);
		$stmt->execute();
		if ($get_id != $user_id) {
			$this->sendNotification($get_id, $user_id, $tweet_id, 'like');
		}
	}

	public function dislike($user_id, $postid, $getid)
	{
		$query = "UPDATE tweets SET likesCount = likesCount -1 WHERE tweetID=:post_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':post_id', $postid, PDO::PARAM_INT);
		$stmt->execute();
		//deleting from likes
		$Sqlquery = "DELETE FROM likes WHERE likeBy=:like_by AND likeOn=:like_on";
		$stmt = $this->pdo->prepare($Sqlquery);
		$stmt->bindParam(':like_by', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':like_on', $postid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function trends()
	{
		$query = "SELECT *, COUNT(tweetID) AS tweetsCount 
		FROM trends INNER JOIN tweets ON status LIKE 
		CONCAT('%#', hashtag , '%') OR retweetMsg LIKE 
		CONCAT('%#', hashtag , '%') GROUP BY hashtag ORDER BY tweetID";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute();

		$trends = $stmt->fetchAll(PDO::FETCH_OBJ);
		echo '<div class="trend-wrapper"><div class="trend-inner"><div class="trend-title"><h3>Trends</h3></div>';
		foreach ($trends as $trend) {
			echo '<div class="trend-body">
			<div class="trend-body-content">
				<div class="trend-link">
					<a href="' . BASE_URL . 'hashtag/' . $trend->hashtag . '">#' . $trend->hashtag . '</a>
				</div>
				<div class="trend-tweets">
					' . $trend->tweetsCount . '<span>tweets</span>
				</div>
			</div>
		</div>';
		}
		echo '</div></div>';
	}

	public function getTweetsByHash($hashtag)
	{
		$query = "SELECT * FROM tweets LEFT JOIN users ON 
		tweetBy = user_id WHERE status LIKE :hashtag OR retweetMsg LIKE :hashtag";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(":hashtag", '%#' . $hashtag . '%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getUsersByHash($hashtag)
	{
		$query = "SELECT DISTINCT * FROM tweets INNER JOIN users ON tweetBy = user_id
		WHERE status LIKE :hashtag OR retweetMsg LIKE :hashtag GROUP BY user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(":hashtag", '%#' . $hashtag . '%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}
}
