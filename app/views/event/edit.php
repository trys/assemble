<h1>Edit event</h1>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Event Name', check_object( $viewmodel->event, 'name' ), false );?>
	<?php text_input( 'location', 'Location', check_object( $viewmodel->event, 'location' ), false );?>
	<?php hidden_input( 'latlng', check_object( $viewmodel->event, 'latlng' ) );?>
	<?php $start = check_object( $viewmodel->event, 'start' ) ? date( 'c', $viewmodel->event->start ) : '';
	text_input( 'start', 'Event Start', $start, false, array( 'type' => 'datetime-local' ) );?>
	<?php $end = check_object( $viewmodel->event, 'end' ) ? date( 'c', $viewmodel->event->end ) : '';
	text_input( 'end', 'Event End', $end, false, array( 'type' => 'datetime-local' ) );?>
	<input type="submit" value="Go!" class="button" />
</form>