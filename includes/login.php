<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
	header('Location:../index.php');
}
if (isset($_POST['login']) && !empty($_POST['login'])) {

	$email = $_POST['email'];
	$password = $_POST['password'];

	if (!empty($email) or !empty($password)) {
		$email = $getFromU->CheckInput($email);
		$password = $getFromU->CheckInput($password);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = "Invalid Email";
		} else {
			if ($getFromU->login($email, $password) === false) {
				$error = "The email or password is incorrect";
			}
		}
	} else {
		$error = "Please enter username and passowrd";
	}
}

?>
<div class="login-div">
	<form method="post">
		<ul>
			<li>
				<input type="text" name="email" placeholder="Please enter your Email here" />
			</li>
			<li>
				<input type="password" name="password" placeholder="password"><input type="submit" name="login" value="Log in">
			</li>
			<li>
				<input type="checkbox" Value="Remember me">Remember me
			</li>
		</ul>
		<?php
		if (isset($error)) {
			echo '<p class="error-li">
	  <div class="span-fp-error">' . $error . '</div>
	 </p>';
		}
		?>
	</form>
</div>