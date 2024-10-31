<?php
/**
 * @package search123
 * @author Stefanie Brandstätter & Tim Zylinski
 * @version 2.4.1
 */

/*
 Plugin Name: Search123
 Plugin URI: http://www.search-123.de/plugins/
 Description: Search123 is a Wordpress plugin for managing Search123 ads on your blog. The appearance of the ads (color, font and font size) can be customized.
 Author: Stefanie Brandstätter & Tim Zylinski
 Author URI: http://www.search-123.de
 Version: 2.4.1
 */

if ( function_exists ('add_action') ) {
	// Pre-2.6 compatibility
	if ( !defined('WP_CONTENT_URL') )
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined( 'WP_PLUGIN_URL' ) )
	define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( !defined( 'WP_PLUGIN_DIR' ) )
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

	// Guess the location
	define('S123_PLUGIN_BASENAME', plugin_basename(__FILE__) );
	define('S123_PLUGIN_URL', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));
	define('S123_PLUGIN_FILENAME', str_replace(S123_PLUGIN_URL.'/', '', plugin_basename(__FILE__) ) );
	define('S123_TEXTDOMAIN', 'search123');
}

// include the search123 class
require_once(dirname(__FILE__) . '/php/search123.class.php');

/**
 * Images/ Icons in base64-encoding
 */
if ( isset($_GET['resource']) && !empty($_GET['resource']) ) {
	# base64 encoding performed by base64img.php from http://php.holtsmark.no
	$resources = array(
		'search123.gif' =>
		'R0lGODlhCAAIAIcAMf///93n6nmmvWvJ96fo/+/6+v7//uPi5C'.
		'pceUiQpHa7qJ7J5+j2+v7+/f/+/2tzewcwVWmlQbvnhf/+/Pj4'.
		'9/38/Pr6+y80SR04Rm6bIuj44fX39jo+TBI0T1uQGavfbvv8/v'.
		'v9/P/+/e30+YuLkAAkSzqCZ2fEDsntnP/7/vz9+cTd8PLu70Fj'.
		'dgZgjyupyGrZ7NL4/r3Z8MHb8efu72ycsSym2TzO/pXT7brQ6v'.
		'3+/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
		'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAACA'.
		'AIAAcISwABBBAwgEABAwAOIEigYAGDBg4eQIggYQKFChYuYMig'.
		'AUADABs4dPDwAUQIESNIlDBxAkUKFStYtHDxAkYMGTMA0Khh4w'.
		'aOHDoCAgA7'.
		''
	);

	if ( array_key_exists($_GET['resource'], $resources) ) {

		$content = base64_decode($resources[ $_GET['resource'] ]);

		$lastMod = filemtime(__FILE__);
		$client = ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false );
		// Checking if the client is validating his cache and if it is current.
		if ( isset($client) && (strtotime($client) == $lastMod) ) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 304);
			exit;
		} else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 200);
			header('Content-Length: '.strlen($content));
			header('Content-Type: image/' . substr(strrchr($_GET['resource'], '.'), 1) );
			echo $content;
			exit;
		}
	}
}

// create a class object
if ( class_exists('search123') && function_exists('is_admin') ) {
	$search123o = new search123();
}

// include the search123 widget class if s123 class is ready
if ( class_exists('search123') ) {
	require_once(dirname(__FILE__) . '/php/search123widget.class.php');
}

/**
 * map the old searchFeed_searchpage function to the new getAds
 *
 * @package search123
 */
function searchFeed_searchpage( $size=0, $keyword="", $align="" ) {
	global $search123o;
	$search123o->getAds($size, $keyword, $align);
}
?>