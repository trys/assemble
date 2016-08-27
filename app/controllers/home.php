<?php

class home extends index {

	public function __construct( $params = array() )
	{
		include dirname(__file__) . '/../models/event.php';

		$viewmodel = new ViewModel( array( 'title' => 'Home' ) );
		
		
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
		$response = $firebase->get( DEFAULT_PATH . '/events/' );
		$response = json_decode( $response );

		$viewmodel = new ViewModel( array( 'title' => 'Events', 'events' => array() ) );

		if ( $response ) {
			foreach ( get_object_vars( $response ) as $event_id => $event_response ) {
				if ( $event_response->start < time() ) {
					continue;
				}

				$event_response->id = $event_id;
				$viewmodel->events[] = new EventModel( $event_response );
			}

			if ( $viewmodel->events ) {
				$viewmodel->events = order_object( $viewmodel->events );
			}
		}

		$this->load_view( 'home/index', $viewmodel );
	}

}