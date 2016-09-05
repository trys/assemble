<div class="primary">
	<h1>Add more detail about the event</h1>
	<form method="POST">

		<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
			<ul class="errors">
				<?php foreach ( $errors as $error ) :?>
					<li><?php echo $error;?></li>
				<?php endforeach;?>
			</ul>
		<?php endif;?>

		<?php text_input( 'name', 'Event Name', check_object( $viewmodel->event, 'name' ), true, array( 'autofocus' => '1' ) );?>
		<?php text_input( 'location', 'Location', check_object( $viewmodel->event, 'location' ), true );?>
		<?php hidden_input( 'latlng', check_object( $viewmodel->event, 'latlng' ) );?>
		
		<?php $start = check_object( $viewmodel->event, 'start' ) ? date( 'Y-m-d\TH:i:s', $viewmodel->event->start ) : '';
		text_input( 'start', 'Event Start', $start, true, array( 'type' => 'datetime-local' ) );?>
		
		<?php $end = check_object( $viewmodel->event, 'end' ) ? date( 'Y-m-d\TH:i:s', $viewmodel->event->end ) : '';
		text_input( 'end', 'Event End', $end, true, array( 'type' => 'datetime-local' ) );?>
		
		<?php textarea_input( 'long_desc', 'Event Description', check_object( $viewmodel->event, 'long_desc' ), false );?>
		<?php text_input( 'short_desc', 'Event Summary', check_object( $viewmodel->event, 'short_desc' ), false );?>
		<?php text_input( 'host', 'Event Host', check_object( $viewmodel->event, 'host' ), false );?>
		
		<?php
		$tags = check_object( $viewmodel->event, 'tags', array() );
		multiline_input( 'tags[]', 'Tags', $tags );
		?>

		<?php
		$guestlist = check_object( $viewmodel->event, 'guestlist', array() );
		multiline_input( 'guestlist[]', 'Guestlist', $guestlist );
		?>

		<input type="submit" value="Save" class="button" /> <a href="<?php echo url( 'event', $viewmodel->event->id );?>" class="button">Cancel</a>
	</form>
</div>