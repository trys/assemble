<?php
class assemble
{
	function __construct()
	{
		$this->setup();
		$this->route();
	}

	public function setup()
	{
		session_start();
		date_default_timezone_set( 'Europe/London' );

		require __DIR__ . '/../vendor/autoload.php';

		include 'config.php';
		
		include 'functions.php';
		include 'api.php';
		include 'controllers/index.php';
		include 'models/index.php';
		include 'viewmodels/index.php';
	}

	public function route()
	{

		$path = ( empty( $_GET[ 'path' ] || $_GET[ 'path' ] === 'index' ) ) ? 'home' : htmlentities( trim( $_GET[ 'path' ] ) );

		$params = explode( '/', $path );
		foreach ( $params as $k => $v ) {
			if ( ! $v ) {
				unset( $params[ $k ] );
			}
		}

		if ( $params && ( strpos($params[0], 'event?') === 0 || strpos($params[0], 'register?') === 0 || ( isset( $params[1] ) && strpos($params[1], 'create?') === 0 ) ) ) {

			if ( isset( $params[1] ) && strpos($params[1], 'create?') === 0 ) {
				$params = explode( '?', $params[0] );
				$params = array( $params[0], 'create' );
			} else {
				$params = explode( '?', $params[0] );
				$params = array( $params[0] );
			}
		}

		$overwrites = array();

		if ( in_array( $params[ 0 ], $overwrites ) ) {
			$controller = array_search( $params[ 0 ], $overwrites );
		} else {
			$controller = file_exists( dirname( __FILE__ ) . '/controllers/' . $params[ 0 ] . '.php' ) ? $params[ 0 ] : 'error';
		}

		include dirname( __FILE__ ) . '/controllers/' . $controller . '.php';
		unset( $params[ 0 ] );

		$this->controller = new $controller( $params );	
		
	}
}
new assemble();