<h1><?php echo esc( check_object( $viewmodel->user, 'name' ) );?></h1>

<?php
$job = check_object( $viewmodel->user, 'job_title' );
$employer = check_object( $viewmodel->user, 'employer' );
$website = check_object( $viewmodel->user, 'website' );

if ( $job || $employer ) {
	echo '<h2>' . esc( 'Works ' . ( $employer ? 'at ' . $employer . ' ' : '' ) . ( $job ? 'as a ' . $job : '' ) ) . '</h2>';
}

if ( $website ) {
	echo '<h3><a href="' . esc( $website ) . '">' . esc( $website ) . '</a></h3>';
}


if ( $viewmodel->user->can_be_edited() ) :?>
	<p><a href="<?php echo url( 'account', 'edit' );?>">Edit</a></p>
<?php endif;?>