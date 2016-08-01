<h1><?php echo esc( $viewmodel->event->name );?></h1>
<h2><time datetime="<?php echo date( 'c', $viewmodel->event->start );?>"><?php format_date( $viewmodel->event->start );?></time> - <time datetime="<?php echo date( 'c', $viewmodel->event->start );?>"><?php format_date( $viewmodel->event->end );?></time></h2>

<h3><?php echo esc( $viewmodel->event->location );?></h3>

<?php if ( $long_desc = check_object( $viewmodel->event, 'long_desc' ) ) :?>
	<?php paragraph( $long_desc );?>
<?php endif;?> 

<?php if ( $viewmodel->event->can_be_edited() ) :?>
	<p><a href="<?php echo url( 'event', $viewmodel->event->id, 'edit' );?>">Edit</a></p>
<?php endif;?>