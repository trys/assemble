<h1>Edit your account</h1>
<form method="POST">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Your Name', check_object( $viewmodel->user, 'name' ), true, array( 'autofocus' => '1' ) );?>
	<?php text_input( 'email', 'Email Address', check_object( $viewmodel->user, 'email' ), true, array( 'type' => 'email' ) );?>
	<?php text_input( 'job_title', 'Job Title', check_object( $viewmodel->user, 'job_title' ), false );?>
	<?php text_input( 'employer', 'Employer', check_object( $viewmodel->user, 'employer' ), false );?>
	<?php text_input( 'website', 'Website', check_object( $viewmodel->user, 'website' ), false );?>

	<input type="submit" value="Save" class="button" />
</form>