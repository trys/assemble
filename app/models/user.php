<?php

class User
{
	
	function __construct( $args = array() )
	{
		$this->name = filter_var( check_entity( $args, 'name' ), FILTER_SANITIZE_STRING );
		$this->email = filter_var( check_entity($args, 'email'), FILTER_SANITIZE_EMAIL );
		$this->job_title = filter_var( check_entity( $args, 'job_title' ), FILTER_SANITIZE_STRING );
		$this->employer = filter_var( check_entity( $args, 'employer' ), FILTER_SANITIZE_STRING );
		$this->website = filter_var( check_entity( $args, 'website' ), FILTER_SANITIZE_STRING );
		$this->password = check_entity( $args, 'password' );
	}

	public $id = '';
	public $name = '';
	public $email = '';
	public $password = '';
	public $job_title = '';
	public $employer = '';
	public $website = '';
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

		if ( ! $this->errors ) {
			$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
			$response = $firebase->get( DEFAULT_PATH . '/users', array( 'orderBy' => '"email"', 'equalTo' => json_encode( $this->email ) ) );
			$response = json_decode( $response );
			if ( $response ) {
				$this->errors[ 'email' ] = 'This email address is already in use';	
			}
		}

		if ( ! $this->password ) {
			$this->errors[ 'password' ] = 'Please provide your password';
		} else {
			// http://regexlib.com/REDetails.aspx?regexp_id=31
			preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,100}$/', $this->password, $match);
			if ( ! $match ) {
				$this->errors[ 'password' ] = 'Please make sure your password is 8+ characters and contains at least a number and a uppercase letter';
			}
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
		$response = json_decode( $response );
		if ( ! $response ) {
			$this->errors[ 'email' ] = 'Please double-check your login details';
			return $this->errors;
		}

		$this->decoded_response = $response;

		return $this->errors ? $this->errors : false;
	}

	public function validate_update()
	{
		if ( ! $this->email || ! filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
			$this->errors[ 'email' ] = 'Please provide your email address';
		}

		if ( $this->website && ! is_url( $this->website ) ) {
			if ( $this->website && strpos( 'http', $this->website ) !== 0 ) {
				$this->website = 'http://' . $this->website;
				if ( ! is_url( $this->website ) ) {
					$this->errors[ 'website' ] = 'Please provide a valid website address';
				}
			} elseif ( $this->website ) {
				$this->errors[ 'website' ] = 'Please provide a valid website address';
			}
		}

		return $this->errors ? $this->errors : false;
	}

	public function can_be_edited()
	{
		return $this->id && current_user_id() === $this->id;
	}

	public function compare( $array, $name, $filter = FILTER_SANITIZE_STRING )
	{
		if ( ! isset( $array[ $name ] ) ) {
			return;
		}

		if ( $this->{$name} !== $array[ $name ] ) {
			$this->{$name} = filter_var( $array[ $name ], $filter );
		}
	}

	public function hashPassword()
	{
		$this->password = password_hash( $this->password, PASSWORD_DEFAULT );
	}

	public function save()
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

		$user_to_save = array(
			'name' => $this->name,
			'email' => $this->email,
			'password' => $this->password
		);

		$user_to_save = array_map('sanitize', $user_to_save);

		$user_id = $firebase->push( DEFAULT_PATH . '/users', $user_to_save );

		$user_id = json_decode( $user_id );
		$user_to_save->id = $user_id->name;
		unset( $user_to_save[ 'password' ] );

		return $userToSave;
	}

	public function login()
	{
		foreach( $this->decoded_response as $user_id => &$user ) {
			if ( password_verify( $this->password, $user->password ) ) {
				unset( $user->password );
				$user->id = $user_id;
				return $user;
			} else {
				return false;
			}
		}
	}

	public function update()
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

		if ( ! $this->id ) {
			$user_id = current_user_id();
			if ( ! $user_id ) {
				return;
			} else {
				$this->id = $user_id;
			}
		}

		$user_to_save = array(
			'name' => $this->name,
			'email' => $this->email,
			'password' => $this->password,
			'job_title' => $this->job_title,
			'employer' => $this->employer,
			'website' => $this->website
		);

		$user_to_save = array_map('sanitize', $user_to_save);

		$firebase->update( DEFAULT_PATH . '/users/' . $this->id, $user_to_save );

		return $userToSave;
	}

}