<h1>Edit event details</h1>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	
	<input type="submit" value="Save" class="button" />
</form>