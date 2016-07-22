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
	private $errors = array();

	public function validate()
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

	public function hashPassword()
	{
		// $this->password = password_hash( $this->password, PASSWORD_DEFAULT );
	}

}