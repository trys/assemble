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
		} elseif ( count( $params ) === 2 && check_array( $params, 1 ) === 'details' ) {
			$this->details( esc( $params[ 0 ] ) );
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

		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/events/' );
		$response = json_decode( $response );

		if ( $response ) {
			$viewmodel = new ViewModel( array( 'title' => 'Events', 'events' => array() ) );
			foreach ( get_object_vars( $response ) as $event_id => $event_response ) {
				if ( $event_response->start < time() ) {
					continue;
				}

				if ( ! empty( $_GET[ 'lat'] ) ) {
					$lat = esc( check_array( $_GET, 'lat' ) );
					$lng = esc( check_array( $_GET, 'lng' ) );

					if ( ! $event_response->latlng ) {
						continue;
					}

					$event_latlng = explode(',', $event_response->latlng);
					
					$resultDistance = $this->distance($lat, $lng, $event_latlng[ 0 ], $event_latlng[ 1 ]);
					if ( $resultDistance > 20 ) {
						continue;
					} else {
						$event_response->distance = $resultDistance;
					}
				}

				$event_response->id = $event_id;
				$viewmodel->events[] = new EventModel( $event_response );
			}

			if ( ! empty( $_GET[ 'lat'] ) ) {
				$viewmodel->events = order_object( $viewmodel->events, 'distance' );
			}
			
			$this->load_view('event/index', $viewmodel);
		} else {
			$this->load_view('404');
		}

	}

	private function event( $event_id )
	{
		
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/events/' . $event_id );
		$response = json_decode( $response );

		if ( $response ) {
			$response->id = $event_id;
			$event = new EventModel( $response );
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
		$event = new EventModel( $response );

		if ( ! $event->can_be_edited() ) {
			$this->load_view('404');
			return;
		}

		if ( $response ) {
			$viewmodel = new ViewModel( array( 'title' => $response->name, 'event' => $response ) );

			if ( ! empty( $_POST ) ) {
				$event = new EventModel( $_POST );
				$event->id = $event_id;
				if ( $errors = $event->validate_creation() ) {
					$viewmodel->errors = $errors;
					$this->load_view('event/edit', $viewmodel);
					return;
				} else {
					$event->update();
					redirect( 'event', $event_id );
				}
			} else {
				$this->load_view('event/edit', $viewmodel);
			}
		} else {
			$this->load_view('404');
		}
		
	}

	// Credit: https://www.mullie.eu/geographic-searches/
	private function distance( $lat1, $lng1, $lat2, $lng2 )
	{
	    // convert latitude/longitude degrees for both coordinates
	    // to radians: radian = degree * π / 180
	    $lat1 = deg2rad($lat1);
	    $lng1 = deg2rad($lng1);
	    $lat2 = deg2rad($lat2);
	    $lng2 = deg2rad($lng2);

	    // calculate great-circle distance
	    $distance = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng1 - $lng2));

	    // distance in human-readable format:
	    // earth's radius in km = ~6371
	    return 6371 * $distance;
	}

}