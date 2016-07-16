<?php

class home extends index {

	public function __construct( $params = array() )
	{
		include dirname(__file__) . '/../models/event.php';

		$viewmodel = new ViewModel( array( 'title' => 'Home' ) );
		$viewmodel->events = array();
		for ($i=0; $i < 2; $i++) { 
			$viewmodel->events[] = new Event( array( 'title' => 'Nope ' . $i ) );
		}

		$this->load_view( 'home/index', $viewmodel );
	}

}