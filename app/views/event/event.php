<div class="primary">
	<h1><?php echo esc( $viewmodel->event->name );?></h1>
	<h3 class="event-time"><time datetime="<?php echo date( 'c', $viewmodel->event->start );?>"><?php format_date( $viewmodel->event->start );?></time> - <time datetime="<?php echo date( 'c', $viewmodel->event->start );?>"><?php format_date( $viewmodel->event->end );?></time></h3>

	<h4><?php echo esc( $viewmodel->event->location );?>
		<?php if ( $host = check_object( $viewmodel->event, 'host' ) ) :?>
			<br />Hosted by <?php echo esc( $host );?>
		<?php endif;?>
	</h4>

	<div class="content">
		<?php if ( $long_desc = check_object( $viewmodel->event, 'long_desc' ) ) :?>
			<?php paragraph( $long_desc );?>
		<?php endif;?> 

		<?php if ( $viewmodel->event->can_be_edited() ) :?>
			<p><a href="<?php echo url( 'event', $viewmodel->event->id, 'edit' );?>">Edit</a></p>
		<?php endif;?>
	</div>
</div>
<aside class="secondary">
	<div class="map"><div id="map"></div></div>
	<?php if ( $tags = check_object( $viewmodel->event, 'tags' ) ) :
		$tags = array_map('sanitize', $tags);
		?>
		<ul class="tags">
			<?php foreach ( $tags as $tag )	:?>
				<li>
					<a href="/tag/<?php echo str_replace(' ', '-', $tag );?>">#<?php echo str_replace(' ', '-', $tag );?></a>
				</li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>
</aside>