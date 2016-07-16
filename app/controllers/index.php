<?php

class index {

	public function load_view( $view, $viewmodel = null )
	{
		if ( ! $viewmodel ) {
			$viewmodel = new ViewModel();
		}

		$view_dir = dirname( __FILE__ ) . '/../views/';

		require $view_dir . 'global/header.php';

		require file_exists( $view_dir . $view . '.php' ) ? $view_dir . $view . '.php' : $view_dir . 'error/index.php';

		require $view_dir . 'global/footer.php';

	}

}