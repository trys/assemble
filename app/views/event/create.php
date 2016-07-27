<h1>Create an event</h1>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Event Name', check_array( $_POST, 'name' ), false );?>
	<?php text_input( 'location', 'Location', check_array( $_POST, 'location' ), false );?>
	<?php hidden_input( 'latlng', check_array( $_POST, 'latlng' ) );?>
	<?php text_input( 'start', 'Event Start', check_array( $_POST, 'start' ), false, array( 'type' => 'datetime-local' ) );?>
	<?php text_input( 'end', 'Event End', check_array( $_POST, 'end' ), false, array( 'type' => 'datetime-local' ) );?>
	<input type="submit" value="Go!" class="button" />
</form>