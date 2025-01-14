<?php

/**
 * Useful for debugging:
 * print_r( getenv() );
 */

if ( 'tests-mysql' === getenv( 'WORDPRESS_DB_HOST' ) || ! empty( getenv( 'IS_WATCHING' ) ) ) {
	echo 'we are in "wp-env start" development' . PHP_EOL . PHP_EOL . PHP_EOL;
	require 'bootstrap-wp-env.php';
	// we are in wp-env (local), we know it because the host is tests-mysql and the db is tests-wordpress
} else {
	// we are in github actions, in wp-content/plugins/asim-gravity-form-map-field/ folder of  wordpress installation
	echo 'in a regular wp installation, including github actions ' . PHP_EOL . PHP_EOL . PHP_EOL;
	require 'bootstrap-standard-wp.php';
}
return;
