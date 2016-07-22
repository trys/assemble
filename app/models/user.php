<?php

class User
{
	
	function __construct( $args = array() )
	{
		$this->name = filter_var( check_array( $args, 'name' ), FILTER_SANITIZE_STRING );
		$this->email = filter_var( check_array($args, 'email'), FILTER_SANITIZE_EMAIL );
		$this->password = check_array( $args, 'password' );
	}

	public $name = '';
	public $email = '';
	public $password = '';
	public $decoded_response;
	private $errors = array();

	public function validate_registration()
	{
		if ( ! $this->name ) {
			$this->errors[ 'title' ] = 'Please provide your name';
		}

		if ( ! $this->email ) {
			$this->errors[ 'email' ] = 'Please provide your email address';
		} elseif ( ! filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
			$this->errors[ 'email' ] = 'Please provide a valid email address';
		}

		if ( ! $this->password ) {
			$this->errors[ 'password' ] = 'Please provide your password';
		}

		return $this->errors ? $this->errors : false;
	}

	public function validate_login()
	{
		if ( ! $this->email || ! filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
			$this->errors[ 'email' ] = 'Please provide your email address';
		}

		if ( ! $this->password ) {
			$this->errors[ 'password' ] = 'Please provide your password';
		}

		if ( $this->errors ) {
			return $this->errors;
		}

		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/users', array( 'orderBy' => '"email"', 'equalTo' => json_encode( $this->email ) ) );
		
		if ( ! $response ) {
			$this->errors[ 'email' ] = 'Please double-check your login details';
			return $this->errors;
		}

		$this->decoded_response = json_decode( $response );
		
		return $this->errors ? $this->errors : false;
	}

	public function hashPassword()
	{
		$this->password = password_hash( $this->password, PASSWORD_DEFAULT );
	}

	public function save()
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

		$userToSave = array(
			'name' => $this->name,
			'email' => $this->email,
			'password' => $this->password
		);

		$user_id = $firebase->push( DEFAULT_PATH . '/users', $userToSave );

		unset( $userToSave[ 'password' ] );

		return $userToSave;
	}

	public function login()
	{
		foreach( $this->decoded_response as &$user ) {
			if ( password_verify( $this->password, $user->password ) ) {
				unset( $user->password );
				return $user;
			} else {
				return false;
			}
		}
	}

}