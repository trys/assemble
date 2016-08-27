<div class="events">
	<?php if ( $viewmodel->events ) : ?>
		<?php foreach ( $viewmodel->events as $event ) :?>
			<?php require dirname(__file__) . '/../event/preview.php';?>
		<?php endforeach;?>
	<?php else: ?>
		<article class="event-preview event-preview--404">
			<a href="/"><h2 class="title">Nothing Found</h2>
				<span class="button">View Upcoming Events</span>
			</a>
		</article>
	<?php endif;?>
</div>