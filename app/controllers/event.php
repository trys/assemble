<?php

class event extends index {

	public function __construct( $params = array() )
	{
		$params = array_values( $params );
		include_once dirname(__file__) . '/../models/event.php';

		if ( ! $params ) {
			$this->index();
		} elseif ( $params[ 0 ] === 'create' ) {
			$this->create();
		} elseif ( count( $params ) === 1 ) {
			$this->event( esc( $params[ 0 ] ) );
		} elseif ( count( $params ) === 2 && check_array( $params, 1 ) === 'edit' ) {
			$this->edit( esc( $params[ 0 ] ) );
		} else {
			$this->load_view('error');
		}

	}

	private function create()
	{
		$viewmodel = new ViewModel( array( 'title' => 'Create an Event' ) );
		
		if ( ! empty( $_POST ) ) {
			$event = new EventModel( $_POST );
			if ( $errors = $event->validate_creation() ) {
				$viewmodel->errors = $errors;
				$this->load_view('event/create', $viewmodel);
				return;
			} else {
				$event_id = json_decode( $event->save() );
				redirect( 'event', $event_id->name );
			}
		} else {
			$this->load_view('event/create', $viewmodel);
		}
	}

	private function index()
	{

	}

	private function event( $event_id )
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/events/' . $event_id );
		$response = json_decode( $response );
		$response->id = $event_id;

		$event = new EventModel( $response );

		if ( $response ) {
			$viewmodel = new ViewModel( array( 'title' => $response->name, 'event' => $event ) );
			$this->load_view('event/event', $viewmodel);
		} else {
			$this->load_view('404');
		}
		
	}

	private function edit( $event_id )
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/events/' . $event_id );
		$response = json_decode( $response );
		$response->id = $event_id;

		if ( $response ) {
			$viewmodel = new ViewModel( array( 'title' => $response->name, 'event' => $response ) );

			if ( ! empty( $_POST ) ) {
				$event = new EventModel( $_POST );
				if ( $errors = $event->validate_creation() ) {
					$viewmodel->errors = $errors;
					$this->load_view('event/edit', $viewmodel);
					return;
				} else {
					$event_id = json_decode( $event->save() );
					redirect( 'event', $event_id->name );
				}
			} else {
				$event = new EventModel( $response );
				$this->load_view('event/edit', $viewmodel);
			}
		} else {
			$this->load_view('404');
		}
		
	}

}