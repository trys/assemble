<div class="events">
	<?php foreach ( $viewmodel->events as $event ) :?>
		<article class="event-preview">
			<a href="<?php echo url( 'event', $event->id );?>">
				<h2 class="title"><?php echo esc( $event->name );?></h2>
				<h3 class="event-time"><time datetime="<?php echo date( 'c', $event->start );?>"><?php format_date( $event->start );?></time> - <time datetime="<?php echo date( 'c', $event->start );?>"><?php format_date( $event->end );?></time></h3>

				<h4><?php echo esc( $event->location );?>
					<?php if ( $host = check_object( $event, 'host' ) ) :?>
						<br />Hosted by <?php echo esc( $host );?>
					<?php endif;?>
				</h4>

				<?php if ( $short_desc = check_object( $event, 'short_desc' ) ) :?>
					<?php paragraph( $short_desc );?>
				<?php endif;?>

				<br />

				<span class="button">Find out more</span>
			</a>
		</article>
	<?php endforeach;?>
</div>