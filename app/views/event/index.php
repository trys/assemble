<h1>Events</h1>
<?php foreach ( $viewmodel->events as $event ) :?>
	<article class="preview-event">
		<a href="<?php echo url( 'event', $event->id );?>">
			<h2><?php echo esc( $event->name );?></h2>
			<h3><time datetime="<?php echo date( 'c', $event->start );?>"><?php format_date( $event->start );?></time> - <time datetime="<?php echo date( 'c', $event->start );?>"><?php format_date( $event->end );?></time></h3>

			<h4><?php echo esc( $event->location );?></h4>

			<?php if ( $short_desc = check_object( $event, 'short_desc' ) ) :?>
				<?php paragraph( $short_desc );?>
			<?php endif;?>
		</a>
	</article>
<?php endforeach;?>