<h1>Register</h1>
<form method="POST">
	<?php text_input( 'name', 'Your Name', '', false );?>
	<?php text_input( 'email', 'Email Address', '', false, array( 'type' => 'text' ) );?>
	<?php text_input( 'password', 'Password', '', false, array( 'type' => 'password' ) );?>

	<input type="hidden" name="method" value="register" />
	<input type="submit" value="Register" class="button" />
</form>