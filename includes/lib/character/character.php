<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Character {
	private static $all_characters;

	public $id;
	public $ID;
	public $name;
	public $status;
	public $record;
	public $sign_in_days_count;
	public $last_sign_in_date;
	public $last_status;

	public function __construct( $user, $type = 'current' ) {
		$character = is_numeric( $user ) ? get_user_by( 'id', $user ) : $user;
		$this->id = $character->ID;
		$this->ID = $character->ID;
		$this->display_name = $character->display_name;
		$this->name = $this->get_name();
		$this->sign_in_days_count = $this->get_sign_in_days_count();
		$this->last_sign_in_date = $this->get_last_sign_in_date();
		$this->status = $this->is_first_sign_in_today() || $type == 'other' ? new BLQU_Status( $this->id ) : BLQU_Status::get_instance_from_array( $this->get_current_status() );
		$this->record = new BLQU_Record( $this->id );

		if ( $type == 'current' ) {
			$last_status = $this->is_first_sign_in_today() ? $this->get_current_status() : $this->get_last_status();
			$this->last_status = BLQU_Status::get_instance_from_array( $last_status );
		}

		if ( $this->is_first_sign_in_today() && $type == 'current' ) {
			$this->set_sign_in_days_count();
			$this->set_last_sign_in_date();
			$this->set_last_status();
			$this->set_current_status();
			$this->set_current_record();
		}

	}

	public static function get_current_character() {
		$user = wp_get_current_user();
		$current_character = new self( $user, 'current' );
		return $current_character;
	}

	public static function get_all_characters() {
		if ( ! isset( self::$all_characters ) ) {
			$args = array(
				'role__in' => array( 'author', 'administrator' ),
				'orderby'  => 'ID',
				'order'    => 'ASC',
			);
			$authors = get_users( $args );
			$all_characters = array();
			foreach ( $authors as $author ) {
				$all_characters[] = new self( $author, 'other' );
			}
			self::$all_characters = $all_characters;
		}
		return self::$all_characters;
	}

	public function is_first_sign_in_today() {
		return $this->last_sign_in_date != blqu_get_current_date();
	}

	public function get_name() {
		$nickname = $this->get_nickname();
		return $nickname ? $nickname : $this->display_name;
	}

	public function get_nickname() {
		$blqu_nickname = get_user_meta( $this->id, 'blqu_nickname', true );
		return $blqu_nickname ? $blqu_nickname : '';
	}

	/**
	 * Get the ranking of a parameter in all users.
	 *
	 * @param string strength or defense or agility.
	 * @return int
	 */
	public function get_status_ranking( $parameter ) {
		$character_ids = BLQU_Status::get_characters_parameter_ranking( $parameter, 'ids' );
		$ranking = array_search( $this->id, $character_ids ) + 1;
		return $ranking;
	}

	public function get_sign_in_days_count() {
		$sign_in_days_count = get_user_meta( $this->id, 'sign_in_days_count', true );
		return $sign_in_days_count ? (int) $sign_in_days_count : 0;
	}

	public function get_last_sign_in_date() {
		return get_user_meta( $this->id, 'last_sign_in_date', true );
	}

	public function get_current_status() {
		$current_status = get_user_meta( $this->id, 'current_status', true );
		if ( ! $current_status ) {
			$current_status = array();
			foreach ( BLQU_Status::PARAMETERS as $parameter ) {
				$current_status[ $parameter ] = 0;
			}
		}

		return $current_status;
	}

	public function get_last_status() {
		$last_status = get_user_meta( $this->id, 'last_status', true );
		if ( ! $last_status ) {
			$last_status = array();
			foreach ( BLQU_Status::PARAMETERS as $parameter ) {
				$last_status[ $parameter ] = 0;
			}
		}

		return $last_status;
	}

	public function get_current_record() {
		$current_record = get_user_meta( $this->id, 'current_record', true );
		if ( ! $current_record ) {
			$current_record = array();
			foreach ( BLQU_Record::ITEMS as $parameter ) {
				$current_record[ $parameter ] = 0;
			}
		}

		return $current_record;
	}

	public function set_nickname( $text ) {
		update_user_meta( $this->id, 'blqu_nickname', $text);
	}

	/**
	 * Update the number of sign-in days and the last sign-in date once a day.
	 *
	 * @return void
	 */
	public function set_sign_in_days_count() {
		$sign_in_days_count = $this->sign_in_days_count;
		$sign_in_days_count += 1;
		update_user_meta( $this->id, 'sign_in_days_count', $sign_in_days_count );
		$this->sign_in_days_count = $sign_in_days_count;
	}

	public function set_last_sign_in_date() {
		update_user_meta( $this->id, 'last_sign_in_date', blqu_get_current_date());
	}

	public function set_current_status() {
		$current_status = $this->status->convert_array();
		update_user_meta( $this->id, 'current_status', $current_status );
	}

	public function set_last_status() {
		$current_status = $this->last_status->convert_array();
		update_user_meta( $this->id, 'last_status', $current_status );
	}

	public function set_current_record() {
		$current_record = $this->record->convert_array();
		update_user_meta( $this->id, 'current_record', $current_record );
	}

	public function calculate_difference_current_parameter_between_last_parameter( $parameter ) {
		$current_parameter = $this->status->$parameter;
		$last_parameter = $this->last_status->$parameter;
		return $current_parameter - $last_parameter;
	}

	public function get_top_of_parameter_name_and_value() {
		$current_status = $this->status->convert_array();
		arsort( $current_status );
		foreach ( $current_status as $key => $value ) {
			return array( $key, $value );
		}
	}
}