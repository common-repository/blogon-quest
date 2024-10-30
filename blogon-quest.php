<?php
/*
Plugin Name: Blogon Quest
Plugin URI:
Description: This is a plugin that turns your daily writing activities into an RPG and makes writing articles fun just like playing game..
Author: PRESSMAN
Author URI: https://www.pressman.ne.jp/
Text Domain: blogon-quest
Domain Path: /languages
Version: 1.0.0
License: GPL2
Requires at least: 4.9
Requires PHP: 5.6
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'BLOGON_QUEST_DIRECTORY_PATH' ) ) {
	define( 'BLOGON_QUEST_DIRECTORY_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BLOGON_QUEST_DIRECTORY_URL' ) ) {
	define( 'BLOGON_QUEST_DIRECTORY_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BLOGON_QUEST_PLUGIN_BASE_NAME' ) ) {
	define( 'BLOGON_QUEST_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'BLOGON_QUEST_DIRECTORY_NAME' ) ) {
	define( 'BLOGON_QUEST_DIRECTORY_NAME', dirname( BLOGON_QUEST_PLUGIN_BASE_NAME ) );
}

function blqu_get_path( $filename = '' ) {
	return BLOGON_QUEST_DIRECTORY_PATH . ltrim( $filename, '/' );
}

function blqu_get_url( $filename = '' ) {
	return BLOGON_QUEST_DIRECTORY_URL . ltrim( $filename, '/' );
}

function blqu_get_stylesheet_url( $filename = '' ) {
	return blqu_get_url( 'assets/styles/' . $filename );
}

function blqu_get_image_url( $filename = '' ) {
	return blqu_get_url( 'assets/images/' . $filename );
}

function blqu_include( $filename = '' ) {
	$file_path = blqu_get_path( $filename );
	if ( file_exists( $file_path ) ) {
		include_once( $file_path );
	}
}

function blqu_get_ordinal_number( $num ) {
	$n = $num % 10;

	switch( $n ) {
		case 1:
			return "st";
		case 2:
			return "nd";
		case 3:
			return "rd";
		default:
			return "th";
	}
}

function blqu_get_current_date() {
	return wp_date( 'Y-m-d' );
}

function blqu_is_japanese() {
	return determine_locale() == 'ja';
}

if ( ! class_exists( 'BlogonQuest' ) ) :
class BlogonQuest {

	private static $instance;

	public function __construct() {
		blqu_include( 'includes/lib/access-counter.php' );
		blqu_include( 'includes/lib/character/character.php' );
		blqu_include( 'includes/lib/character/status.php' );
		blqu_include( 'includes/lib/character/record.php' );
		blqu_include( 'includes/admin/admin.php' );
		blqu_include( 'includes/admin/dashboard.php' );
		blqu_include( 'includes/admin/pages/character-list-table.php' );
		blqu_include( 'includes/admin/pages/home.php' );
		blqu_include( 'includes/admin/pages/profile.php' );
		blqu_include( 'includes/admin/pages/ranking.php' );
		blqu_include( 'includes/admin/pages/help.php' );
		blqu_include( 'includes/admin/menu.php' );
		blqu_include( 'includes/lib/cron.php' );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function load_plugin_textdomain(){
    load_plugin_textdomain( 'blogon-quest', false, BLOGON_QUEST_DIRECTORY_NAME . '/languages' );
  }

	public function add_body_class( $classes ) {
		$new_classes = $classes . ' blogon-quest';
		return wp_is_mobile() ? $new_classes . ' blogon-sp' : $new_classes . ' blogon-pc';
	}

	public function activate() {
		BLQU_Record::set_all_characters_record();
	}

}
endif;

BlogonQuest::get_instance();