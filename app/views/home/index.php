<h1>Home</h1>
<?php if ( $user = is_user_logged_in() ) :?>
	<h2>Logged in</h2>
	<?php p( $user );?>
<?php else:?>
	<h2>Logged out</h2>
<?php endif;?>
<?php if ( $events = check_object( $viewmodel, 'events' ) ) :?>
	<?php foreach ( $events as $event ) :?>
		<h2><?php echo $event->title;?></h2>
	<?php endforeach;?>
<?php endif;?>