<h1>Home</h1>
<?php if ( $events = check_object( $viewmodel, 'events' ) ) :?>
	<?php foreach ( $events as $event ) :?>
		<h2><?php echo $event->title;?></h2>
	<?php endforeach;?>
<?php endif;?>