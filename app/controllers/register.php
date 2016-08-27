<?php

class register extends index {

	public function __construct( $params = array() )
	{

		if ( ! $params ) {
			$this->index();
		}

	}

	private function index()
	{
		$_SESSION[ 'user' ] = '';
		if ( ! empty( $_POST ) ) {
			if ( check_array( $_POST, 'method' ) === 'login' ) {
				$this->loginUser($_POST);
			} else {
				$this->createUser($_POST);
			}
		} else {
			$viewmodel = new ViewModel( array( 'title' => 'Register' ) );
			$this->load_view( 'register/index', $viewmodel );
		}
	}

	private function createUser( $post_data )
	{

		include dirname(__file__) . '/../models/user.php';

		$viewmodel = new ViewModel( array( 'title' => 'Register' ) );
		$user = new User( $post_data );

		if ( $errors = $user->validate_registration() ) {
			$viewmodel->registration_errors = $errors;
			$this->load_view( 'register/index', $viewmodel );
		} else {
			$user->hashPassword();
			$response = $user->save();

			$_SESSION[ 'user' ] = $response;
			if ( ! empty( $_GET[ 'event_name' ] ) ) {
				$args = array(
					'event_name' => check_array( $_GET, 'event_name' ),
					'location' => check_array( $_GET, 'location' ),
					'latlng' => check_array( $_GET, 'latlng' ),
					'start' => check_array( $_GET, 'start' ),
					'end' => check_array( $_GET, 'end' )
				);

				redirect( 'event', 'create?' . http_build_query( $args ) );
			} else {
				redirect();
			}
		}
	}

	private function loginUser( $post_data )
	{

		include dirname(__file__) . '/../models/user.php';

		$viewmodel = new ViewModel( array( 'title' => 'Register' ) );
		$user = new User( array( 'email' => check_array( $post_data, 'login_email' ), 'password' => check_array( $post_data, 'login_password' ) ) );

		if ( $errors = $user->validate_login() ) {
			$viewmodel->login_errors = $errors;
			$this->load_view( 'register/index', $viewmodel );
		} else {
			
			if ( $loggedIn = $user->login() ) {
				$_SESSION[ 'user' ] = $loggedIn;

				if ( ! empty( $_GET[ 'event_name' ] ) ) {
					$args = array(
						'event_name' => check_array( $_GET, 'event_name' ),
						'location' => check_array( $_GET, 'location' ),
						'latlng' => check_array( $_GET, 'latlng' ),
						'start' => check_array( $_GET, 'start' ),
						'end' => check_array( $_GET, 'end' )
					);

					redirect( 'event', 'create?' . http_build_query( $args ) );
				} else {
					redirect();
				}
			} else {
				$viewmodel->login_errors = array( 'email' => 'Please double-check your login details' );
				$this->load_view( 'register/index', $viewmodel );
				$_SESSION[ 'user' ] = '';
			}
		}
	}

}