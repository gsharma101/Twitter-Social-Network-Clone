<?php
class User
{
	protected $pdo;

	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	function CheckInput($var)
	{
		$var = htmlentities($var);
		$var = trim($var);
		$var = stripcslashes($var);

		return $var;
	}

	public function checkEmail($email)
	{
		$stmt = $this->pdo->prepare("SELECT 'email' FROM 'users' WHERE 'email' = :email");
		$stmt->bindParam(":email", $email, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function preventAccess($request, $currentFile, $currently)
	{
		if ($request == "GET" && $currentFile == $currently) {
			header('Location:' . BASE_URL . 'home.php');
		}
	}

	public function delete($table, $array)
	{
		$sql = "DELETE FROM {$table}";
		$where = " WHERE ";

		foreach ($array as $name => $value) {
			$sql .= "{$where}  {$name} = :{$name}";
			$where = " AND ";
		}

		if ($stmt = $this->pdo->prepare($sql)) {
			foreach ($array as $name => $value) {
				$stmt->bindValue(':' . $name, $value);
			}
			$stmt->execute();
		}
	}


	public function checkUsername($username)
	{
		$stmt = $this->pdo->prepare("SELECT 'username' FROM 'users' WHERE 'username' = :username");
		$stmt->bindParam(":email", $username, PDO::PARAM_STR);
		$stmt->execute();

		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function uploadImage($file)
	{
		$filename = basename($file['name']);
		$file_Temp = $file['tmp_name'];
		$fileSize = $file['size'];
		$error = $file['error'];

		$ext = explode('.', $filename);
		$ext = strtolower(end($ext));

		$allowed_ext = array('jpg', 'jpeg', 'png');

		if (in_array($ext, $allowed_ext) === true) {
			if ($error === 0) {
				if ($fileSize <= 209272152) {
					$fileRoot = 'users/' . $filename;
					move_uploaded_file($file_Temp, $_SERVER['DOCUMENT_ROOT'] . '/twitter/' . $fileRoot);
					return $fileRoot;
				} else {
					$GLOBALS['imageError'] = "The file size is to large";
				}
			}
		} else {
			$GLOBALS['imageError'] = "This extension is not valid";
		}
	}
	public function register($email, $screenName, $password)
	{
		$query = "INSERT INTO users (email,screenName,user_password) VALUES (:email,:screenName,:user_password)";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->bindParam(':screenName', $screenName, PDO::PARAM_STR);
		$stmt->bindParam(':user_password', $password, PDO::PARAM_STR);
		$stmt->execute();

		$user_id = $this->pdo->lastInsertId();
		$_SESSION['user_id'] = $user_id;
	}

	public function userIdByUsername($username)
	{
		$query = "SELECT user_id from users where username=:username";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(":username", $username, PDO::PARAM_STR);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		return $user->user_id;
	}

	public function search($search)
	{
		$query = "SELECT user_id , username , screenName , profileImage , profileCover FROM users WHERE username LIKE ? OR screenName LIKE ?";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindValue(1, $search . '%', PDO::PARAM_STR);
		$stmt->bindValue(2, $search . '%', PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function create($table, $fields = array())
	{
		$columns = implode(',', array_keys($fields));
		$values = ':' . implode(', :', array_keys($fields));
		$sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
		if ($stmt = $this->pdo->prepare($sql)) {
			foreach ($fields as $key => $data) {
				$stmt->bindValue(':' . $key, $data);
			}
			$stmt->execute();
			return $this->pdo->lastInsertId();
		}
	}

	public function update($table, $user_id, $fields = array())
	{
		$columns = '';
		$i = 1;
		foreach ($fields as $name => $value) {
			$columns .= "'{$name}' = :{$name}";
			if ($i < count($fields)) {
				$columns .= ', ';
			}
			$i++;
		}
		$sql = "UPDATE {$table} SET {$columns} WHERE 'user_id'= {$user_id}";
		if ($stmt = $this->pdo->prepare($sql)) {
			foreach ($fields as $key => $value) {
				$stmt->bindValue(':' . $key, $value);
			}
			$stmt->execute();
		}
	}

	public function TimeAgo($datetime)
	{
		$time = strtotime($datetime);
		$currentTime = time();
		$seconds = $currentTime - $time;
		$minute = round($seconds / 60);
		$hour = round($seconds / 3600);
		$month = round($seconds / 2600640);

		if ($seconds <= 60) {
			return 'Just now';
		} elseif ($minute <= 60) {
			return $minute . "m ago";
		} elseif ($hour <= 24) {
			return $hour . "h ago";
		} elseif ($month <= 12) {
			return date('j M', $time);
		} else {
			return date('d M Y h:ia');
		}
	}

	public function  loggedIn()
	{
		return (isset($_SESSION['user_id'])) ? true : false;
	}


	public function login($email, $password)
	{

		$query = "SELECT * FROM users WHERE email=:email";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		$checkPassword = password_verify($password, $user['user_password']);
		if ($checkPassword === false) {
			return false;
		} else {
			session_start();
			$_SESSION['user_id'] = $user['user_id'];
			header('Location: home.php');
			exit();
		}
	}

	public function userData($user_id)
	{
		$query = "SELECT * from users WHERE user_id=:user_id";
		$stmt = $this->pdo->prepare($query);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function sendNotification($get_id, $user_id, $target, $type)
	{
		$stmt = $this->pdo->prepare("INSERT INTO notification (notificationFor,notificationFrom,target,type,time) VALUES (:notificationFor,:notificationFrom,:target,:type,:time)");
		$stmt->bindParam(':notificationFor', $get_id, PDO::PARAM_INT);
		$stmt->bindParam(':notificationFrom', $user_id, PDO::PARAM_INT);
		$stmt->bindParam(':target', $target, PDO::PARAM_INT);
		$stmt->bindParam(':type', $type, PDO::PARAM_STR);
		$stmt->bindParam(':time', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$stmt->execute();
	}
}
