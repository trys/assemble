<?php if ( ! empty( $_GET[ 'event_name' ] ) ) :?>
	<h3>You need to be registed or logged in to create an event.</h3>
	<p>Don't worry, we've saved your event for when you log in!</p>
<?php endif;?>

<div class="primary primary--thin">
	<h1>Register</h1>
	<form method="POST">

		<?php if ( $errors = check_object( $viewmodel, 'registration_errors' ) ) :?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) :?>
					<li><?php echo $error;?></li>
				<?php endforeach;?>
			</ul>
		<?php endif;?>

		<?php text_input( 'name', 'Your Name', check_array( $_POST, 'name' ), true );?>
		<?php text_input( 'email', 'Email Address', check_array( $_POST, 'email' ), true, array( 'type' => 'email' ) );?>
		<?php text_input( 'password', 'Password', '', true, array( 'type' => 'password', 'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,100}' ) );?>

		<?php hold_event();?>

		<input type="hidden" name="method" value="register" />
		<input type="submit" value="Register" class="button" />
	</form>
</div>

<div class="secondary secondary--thin">
	<h2 class="title">Login</h2>
	<form method="POST">

		<?php if ( $errors = check_object( $viewmodel, 'login_errors' ) ) :?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) :?>
					<li><?php echo $error;?></li>
				<?php endforeach;?>
			</ul>
		<?php endif;?>

		<?php text_input( 'login_email', 'Email Address', check_array( $_POST, 'login_email' ), true, array( 'type' => 'email' ) );?>
		<?php text_input( 'login_password', 'Password', '', true, array( 'type' => 'password' ) );?>

		<?php hold_event();?>

		<input type="hidden" name="method" value="login" />
		<input type="submit" value="Login" class="button" />
	</form>
</div>