<?php
// router.php
if (preg_match('/\.(?:png|jpg|jpeg|css|js)$/', $_SERVER[ 'REQUEST_URI' ])) {
    return false;
} else {
	$route = ltrim( $_SERVER[ 'REQUEST_URI' ], '/' );
    $_GET[ 'path' ] = $route;
    return false;
}
?>