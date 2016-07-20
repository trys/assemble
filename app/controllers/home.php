<?php

class home extends index {

	public function __construct( $params = array() )
	{
		include dirname(__file__) . '/../models/event.php';

		$viewmodel = new ViewModel( array( 'title' => 'Home' ) );
		
		

		$this->load_view( 'home/index', $viewmodel );
	}

}