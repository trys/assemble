<h1>Register</h1>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'registration_errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Your Name', '', false );?>
	<?php text_input( 'email', 'Email Address', '', false, array( 'type' => 'text' ) );?>
	<?php text_input( 'password', 'Password', '', false, array( 'type' => 'password' ) );?>

	<input type="hidden" name="method" value="register" />
	<input type="submit" value="Register" class="button" />
</form>


<h2>Login</h2>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'login_errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'email', 'Email Address', '', false, array( 'type' => 'email' ) );?>
	<?php text_input( 'password', 'Password', '', false, array( 'type' => 'password' ) );?>

	<input type="hidden" name="method" value="login" />
	<input type="submit" value="Login" class="button" />
</form>