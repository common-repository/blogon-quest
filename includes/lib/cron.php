<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Using WP Cron.
 */
class BLQU_Cron {
	private static $instance;

	public function __construct() {
		$this->add_schedule_event( 'set_all_author_status_event', 'set_all_author_status', 'daily' );
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function add_schedule_event( $event_name, $event, $interval ) {
		add_action( $event_name, array( $this, $event ) );

		if ( ! wp_next_scheduled( $event_name ) ) {
			wp_schedule_event( time(), $interval, $event_name );
		}
	}

	public function set_all_author_status() {
		BLQU_Status::set_all_characters_status();
	}
}

$blqu_batch_processing = BLQU_Cron::get_instance();