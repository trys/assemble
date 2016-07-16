<?php

class Api {

	private $url = 'http://api.assemble.dev/';
	private $default_headers;

	function __construct()
	{

		$this->default_headers = array(
			'Content-Type' => 'application/json',
			'Cache-control' => 'no-cache'
		);
		
	}


	private function prepare_headers( $custom_headers = array() )
	{
		$headers = array();
		$combined_headers = array_merge( $this->default_headers, $custom_headers );
		foreach ( $combined_headers as $k => $v ) {
			$headers[] = $k . ': ' . $v;
		}
		return $headers;
	}


	public function get( $endpoint = '' )
	{
		return $this->send( $endpoint, array(), array(), 'GET' );
	}


	public function post( $endpoint = '', $data = array(), $custom_headers = array() )
	{
		if ( ! $custom_headers ) {
			$custom_headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
		}

		return $this->send( $endpoint, $data, $custom_headers, 'POST' );
	}


	public function put( $endpoint = '', $data = array(), $custom_headers = array() )
	{
		if ( ! $custom_headers ) {
			$custom_headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
		}

		return $this->send( $endpoint, $data, $custom_headers, 'PUT' );
	}


	public function delete( $endpoint = '', $data = array(), $custom_headers = array() )
	{
		if ( ! $custom_headers ) {
			$custom_headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
		}

		return $this->send( $endpoint, $data, $custom_headers, 'DELETE' );
	}


	private function send( $endpoint, $data, $custom_headers = array(), $method = 'GET' )
	{
		$headers = $this->prepare_headers( $custom_headers );
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->url . $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query( $data ),
			CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		return $err ? false : json_decode( $response );
	}

}