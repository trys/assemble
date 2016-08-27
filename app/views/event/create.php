<h1>Create an event</h1>
<form method="POST" class="primary">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Event Name', check_array( $_POST, 'name' ) ?: check_array( $_GET, 'event_name' ), false );?>
	<?php text_input( 'location', 'Location', check_array( $_POST, 'location' ) ?: check_array( $_GET, 'location' ), false );?>
	<?php hidden_input( 'latlng', check_array( $_POST, 'latlng' ) ?: check_array( $_GET, 'latlng' ) );?>
	<?php
	$start = check_array( $_POST, 'start' );
	if ( ! $start ) {
		$start = check_array( $_GET, 'start' );
		if ( $start ) {
			$start = date( 'Y-m-d\TH:i:s', esc( $start ) );
		}
	}
	text_input( 'start', 'Event Start', $start, false, array( 'type' => 'datetime-local' ) );?>
	<?php
		$start = check_array( $_POST, 'end' );
		if ( ! $end ) {
			$end = check_array( $_GET, 'end' );
			if ( $end ) {
				$end = date( 'Y-m-d\TH:i:s', esc( $end ) );
			}
		}
		text_input( 'end', 'Event End', $end, false, array( 'type' => 'datetime-local' ) );?>
	<input type="submit" value="Go!" class="button" />
</form>