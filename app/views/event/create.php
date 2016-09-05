<h1>Create an event</h1>
<form method="POST" class="primary">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Event Name', check_array( $_POST, 'name' ) ?: check_array( $_GET, 'event_name' ), true, array( 'autofocus' => '1' ) );?>
	<?php text_input( 'location', 'Location', check_array( $_POST, 'location' ) ?: check_array( $_GET, 'location' ), true );?>
	<?php hidden_input( 'latlng', check_array( $_POST, 'latlng' ) ?: check_array( $_GET, 'latlng' ) );?>
	<?php
	$start = check_array( $_POST, 'start' );
	if ( ! $start ) {
		$start = check_array( $_GET, 'start' );
		if ( $start ) {
			$start = date( 'Y-m-d\TH:i:s', esc( $start ) );
		}
	}
	text_input( 'start', 'Event Start', $start, true, array( 'type' => 'datetime-local', 'placeholder' => 'dd/mm/yyyy, hh:mm' ) );?>
	<?php
		$start = check_array( $_POST, 'end' );
		if ( ! $end ) {
			$end = check_array( $_GET, 'end' );
			if ( $end ) {
				$end = date( 'Y-m-d\TH:i:s', esc( $end ) );
			}
		}
		text_input( 'end', 'Event End', $end, true, array( 'type' => 'datetime-local', 'placeholder' => 'dd/mm/yyyy, hh:mm' ) );?>

	<?php textarea_input( 'long_desc', 'Event Description', check_array( $_POST, 'long_desc' ) ?: check_array( $_GET, 'long_desc' ), false );?>
	<?php text_input( 'short_desc', 'Event Summary', check_array( $_POST, 'short_desc' ) ?: check_array( $_GET, 'short_desc' ), false );?>
	<?php text_input( 'host', 'Event Host', check_array( $_POST, 'host' ) ?: check_array( $_GET, 'host' ), false );?>
	
	<?php
	$tags = check_array( $_POST, 'tags', array() ) ?: check_array( $_GET, 'tags', array() );
	multiline_input( 'tags[]', 'Tags', $tags );
	?>

	<?php
	$guestlist = check_array( $_POST, 'guestlist', array() ) ?: check_array( $_GET, 'guestlist', array() );
	multiline_input( 'guestlist[]', 'Guestlist', $guestlist );
	?>


	<input type="submit" name="submit" value="Go!" class="button" />
</form>