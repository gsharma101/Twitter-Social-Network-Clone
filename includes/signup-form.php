<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('Location:../index.php');
}
if (isset($_POST['Signup'])) {
	$screenName = $_POST['screen_Name'];
	$email = $_POST['Email'];
	$password = $_POST['Password'];
	$error = '';

	if (empty($screenName) or empty($email) or empty($password)) {
		$error = "All fields are required";
	} else {
		$screenName = $getFromU->CheckInput($screenName);
		$email = $getFromU->CheckInput($email);
		$password = $getFromU->CheckInput($password);

		if (!filter_var($email)) {
			$error = "Invalid email entered";
		} elseif (strlen($screenName) > 20) {
			$error = "Name must be between 6-20 character";
		} elseif (strlen($password) < 5) {
			$error = "password is to short";
		} else {
			if ($getFromU->checkEmail($email) === true) {
				$error = "Email is already in use";
			} else {
				$hashPassword = password_hash($password, PASSWORD_DEFAULT);
				$getFromU->register($email, $screenName, $hashPassword);
				header('Location: includes/signup.php?step=1');
				exit();
			}
		}
	}
}
?>
<form method="POST">
	<div class="signup-div">
		<h3>Sign up </h3>
		<ul>
			<li>
				<input type="text" name="screen_Name" placeholder="Full Name" />
			</li>
			<li>
				<input type="email" name="Email" placeholder="Email" />
			</li>
			<li>
				<input type="password" name="Password" placeholder="Password" />
			</li>
			<li>
				<input type="submit" name="Signup" Value="Signup for Twitter">
			</li>
		</ul>
		<?php
		if (isset($error)) {
			echo '<p class="error-li">
	  <div class="span-fp-error">' . $error . '</div>
	 </p>';
		}
		?>
	</div>
</form>
