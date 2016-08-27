<nav class="find-event">
	<button type="button" class="button" id="find-event-toggle">Find Nearby Events</button>
	<form id="find-event">
		<p>
			<label for="location" class="screen-reader-text">Location</label>
			<input type="text" name="location" id="location" required placeholder="Postcode, Town, Etc..." />
		</p>
		<input type="submit" value="Search" id="js-find-event" />
	</form>
</nav>
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