<?php

class account extends index {

	public function __construct( $params = array() )
	{

		if ( ! is_user_logged_in() ) {
			redirect('register');
		}

		$params = array_values( $params );

		include dirname(__file__) . '/../models/user.php';
		$user_id = $params && $params[ 0 ] !== 'edit' ? $params[ 0 ] : current_user_id();

		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/users/' . esc( $user_id ) );
		$response = json_decode( $response );

		if ( ! $response ) {
			$this->load_view( 'error' );
			return;
		}

		$user = new User( $response );
		$user->id = $user_id;

		if ( check_array( $params, 0 ) === 'edit' ) {
			$this->edit( $user );
		} else {
			$this->index( $user );
		}

	}

	private function index( $user )
	{
		$viewmodel = new ViewModel( array( 'title' => 'Account', 'user' => $user ) );
		$this->load_view( 'account/index', $viewmodel );
	}

	private function edit( $user )
	{
		if ( ! empty( $_POST ) ) {

			$user->compare( $_POST, 'name' );
			$user->compare( $_POST, 'email', FILTER_SANITIZE_EMAIL );
			$user->compare( $_POST, 'job_title' );
			$user->compare( $_POST, 'employer' );
			$user->compare( $_POST, 'website' );

			if ( $errors = $user->validate_update() ) {
				$viewmodel = new ViewModel( array( 'title' => 'Account', 'user' => $user ) );
				$viewmodel->errors = $errors;
				$this->load_view( 'account/edit', $viewmodel );
				return;
			}

			$user->update();
			redirect( 'account' );

		} else {
			$viewmodel = new ViewModel( array( 'title' => 'Account', 'user' => $user ) );
			$this->load_view( 'account/edit', $viewmodel );
		}
	}

}