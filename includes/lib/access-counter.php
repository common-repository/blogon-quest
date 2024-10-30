<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Access_Counter {
	private static $instance;

	public function __construct() {
		add_filter( 'get_header', array( $this, 'access_count' ) );
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Count how many people accessed posts.
	 *
	 * @return void
	 */
	public function access_count() {
		$id = 0;
		if ( is_single() && 'post' == get_post_type() ) {
			$id = get_the_ID();
			self::set_post_page_view( $id );
			self::set_post_session( $id );
		}
		self::set_post_power_to_lead( $id );
		self::set_cookie( $id );
	}

	/**
	 * Get page views each posts or total number of page views.
	 *
	 * @param array $posts
	 * @param string $return_type
	 * @return mixed
	 */
	public static function get_post_page_views( $posts, $return_type = 'sum' ) {
		$page_views = array();

		foreach ( $posts as $post ) {
			$page_view = self::get_post_page_view( $post );
			$page_views[ $post->ID ] = $page_view;
		}

		return 'sum' == $return_type ? array_sum( $page_views ) : $page_views;
	}

	/**
	 * Get sessions each posts or total number of sessions.
	 *
	 * @param array $posts
	 * @param string $return_type
	 * @return mixed
	 */
	public static function get_post_sessions( $posts, $return_type = 'sum' ) {
		$sessions = array();

		foreach ( $posts as $post ) {
			$session = self::get_post_session( $post );
			$sessions[ $post->ID ] = $session;
		}

		return 'sum' == $return_type ? array_sum( $sessions ) : $sessions;
	}

	/**
	 * Get power to lead each posts or total number of power to lead.
	 *
	 * @param array $posts
	 * @param string $return_type
	 * @return mixed
	 */
	public static function get_posts_power_to_leads( $posts, $return_type = 'sum' ) {
		$power_to_leads = array();

		foreach ( $posts as $post ) {
			$power_to_lead = self::get_post_power_to_lead( $post );
			$power_to_leads[ $post->ID ] = $power_to_lead;
		}

		return 'sum' == $return_type ? array_sum( $power_to_leads ) : $power_to_leads;
	}

	public static function get_post_page_view( $post ) {
		$post_id = is_numeric( $post ) ? $post : $post->ID;
		$page_view = get_post_meta( $post_id, 'page_view', true );
		return $page_view ? (int) $page_view : 0;
	}

	public static function get_post_session( $post ) {
		$post_id = is_numeric( $post ) ? $post : $post->ID;
		$session = get_post_meta( $post_id, 'session', true );
		return $session ? (int) $session : 0;
	}

	public static function get_post_power_to_lead( $post ) {
		$post_id = is_numeric( $post ) ? $post : $post->ID;
		$power_to_lead = get_post_meta( $post_id, 'power_to_lead', true );
		return $power_to_lead ? (int) $power_to_lead : 0;
	}

	public static function set_post_page_view( $post_id ) {
		$page_view = self::get_post_page_view( $post_id );
		update_post_meta( $post_id, 'page_view', $page_view + 1 );
	}

	public static function set_post_session( $post_id ) {
		$cookie = self::get_cookie();
		if ( ! $cookie ) {
			$session = self::get_post_session( $post_id );
			update_post_meta( $post_id, 'session', $session + 1);
		}
	}

	public static function set_post_power_to_lead( $post_id ) {
		$cookie = self::get_cookie();
		if ( $cookie && ! ( $post_id && $post_id == $cookie ) ) {
			$power_to_lead = self::get_post_power_to_lead( $cookie );
			update_post_meta( $cookie, 'power_to_lead', $power_to_lead + 1);
		}
	}

	public static function get_cookie() {
		if ( isset( $_COOKIE[ self::cookie_name() ] ) ) {
			return (int) $_COOKIE[ self::cookie_name() ];
		}
	}

	/**
	 * Set cookie for count session or power to lead.
	 *
	 * @param int $post_id
	 * @return void
	 */
	public static function set_cookie( $post_id = 0 ) {
		setcookie( self::cookie_name(), $post_id, time() + 60*30 );
	}

	public static function cookie_name() {
		return 'last_visited_post';
	}
}

$blqu_access_counter = BLQU_Access_Counter::get_instance();