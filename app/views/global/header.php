<!DOCTYPE html>
<html lang="en-gb" class="no-js" xmlns:fb="http://ogp.me/ns/fb#">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<script>
			(function(h){h.className=h.className.replace(/\bno-js\b/,'js')})(document.documentElement);
		</script>
		<title><?php echo esc( $viewmodel->title );?></title>
		<link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="/assets/css/style.css">
	</head>
	<body>
		<header role="banner">
			<a href="/" class="logo">assemble</a>

			<ul>
				<li>
					<a href="/event">find<span> an event</span></a>
				</li>
				<li>
					<a href="/event/create">create<span> an event</span></a>
				</li>
				<?php if ( ! is_user_logged_in() ) :?>
					<li class="desktop">
						<a href="/register">login / register</a>
					</li>
					<li class="mobile">
						<a href="/register">login</a>
					</li>
					<li class="mobile">
						<a href="/register">register</a>
					</li>
				<?php endif;?>
			</ul>
		</header>
		<div class="main">