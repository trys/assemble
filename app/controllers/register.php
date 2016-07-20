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

		if ( $errors = $user->validate() ) {
			$viewmodel->errors = $errors;
			p( $errors );
		} else {
			$user = new User( $post_data );
		}
	}

	private function login( $post_data )
	{

	}

}