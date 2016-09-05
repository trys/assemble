var gulp = require( 'gulp' ),
	sass = require('gulp-sass'),
	maps = require( 'gulp-sourcemaps'),
	uglify = require('gulp-uglify'),
	concat = require( 'gulp-concat'),
	notify = require( 'gulp-notify' ),
	php  = require('gulp-connect-php'),
	browserSync = require( 'browser-sync' ),
	postCSS = {
		core: require( 'gulp-postcss' ),
		prefix: require( 'autoprefixer' )
	};

gulp.task( 'browser-sync', function() {
	browserSync.init(
		[ 'assets/css/style.css', 'assets/js/boot.js', '**/*.php' ],
		{
			proxy: 'assemble.dev',
			notify: true,
			open: false,
			ghostMode: false
		}
	);
});


gulp.task( 'js', function() {
	return gulp.src( ['assets/js/*.js'] )
		// .pipe( maps.init() )
		.pipe(
			concat( 'boot.js' )
		)
		// .pipe( uglify() )
		.on( 'error', onError )
		// .pipe( maps.write( '../../maps/' ) )
		.pipe( gulp.dest( 'assets/js/min' ) );
});


gulp.task( 'scss', function() {
	return gulp.src( 'assets/scss/style.scss' )
		.pipe( maps.init() )
		.pipe(
			sass(
				{
					errLogToConsole: true,
					outputStyle: 'compressed'
				}
			)
			.on( 'error', onError )
		)
		.pipe(
			postCSS.core(
				[
					postCSS.prefix(
						{
							browsers: [
								'ie >= 9',
								'ie_mob >= 10',
								'ff >= 30',
								'chrome >= 34',
								'safari >= 7',
								'opera >= 23',
								'ios >= 7',
								'android >= 4.4',
								'bb >= 10'
							],
							cascade : false,
							remove  : true
						}
					)
				]
			)
		)
		.pipe( maps.write( '../maps/' ) )
		.pipe( gulp.dest( 'assets/css/' ) );
});

gulp.task('php', function() {
	php.server({
		base: './',
		port: 8010,
		keepalive: true,
		bin: '/usr/bin/php',
		ini: '/etc/php5/cli/php.ini',
		router: 'router.php'
	});
});


function onError( err ) {
	notify().write( err );
	this.emit( 'end' );
}


gulp.task( 'default', [ 'browser-sync', 'js', 'scss' ], function () {
	gulp.watch( 'assets/js/boot.js', [ 'js' ] );
	gulp.watch( 'assets/scss/**/*.scss', [ 'scss' ] );
});


gulp.task( 'build', [ 'js', 'scss' ] );

gulp.task( 'serve', [ 'php' ] );