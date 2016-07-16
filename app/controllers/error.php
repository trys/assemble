<?php

class error extends index {

	public function __construct( $params = array() )
	{
		$this->load_view( 'error/index' );
	}

}