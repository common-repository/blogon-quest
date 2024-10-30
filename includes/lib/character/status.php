<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Status {
	public const PARAMETERS = array(
		'strength',
		'defense',
		'agility',
	);

	public $strength;
	public $defense;
	public $agility;

	public function __construct( $author_id = null ) {
		if ( $author_id ) {
			$this->strength = self::get_strength( $author_id );
			$this->defense = self::get_defense( $author_id );
			$this->agility = self::get_agility( $author_id );
		}
	}

	public static function get_instance_from_array( $status_array = array() ) {
		$status = new self;
		foreach ( self::PARAMETERS as $parameter ) {
			$value = $status_array[ $parameter ];
			$status->$parameter = $value ? $value : 0;
		}
		return $status;
	}

	public static function get_strength( $author_id ) {
		$strength = get_user_meta( $author_id, 'strength', true );
		return $strength ? (int) $strength : 0;
	}

	public static function get_defense( $author_id ) {
		$defense = get_user_meta( $author_id, 'defense', true );
		return $defense ? (int) $defense : 0;
	}

	public static function get_agility( $author_id ) {
		$agility = get_user_meta( $author_id, 'agility', true );
		return $agility ? (int) $agility : 0;
	}


	 public static function set_status( $author_id ) {
		$posts = self::get_author_posts( $author_id );
		self::set_strength( $author_id, $posts );
		self::set_defense( $author_id, $posts );
		self::set_agility( $author_id, $posts );
	}

	/**
	 * Update strength.
	 *
	 * Strength is calculated from sessions.
	 *
	 * @param int $author_id
	 * @param array $posts
	 * @return void
	 */
	public static function set_strength( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );
		$session_sum = BLQU_Access_Counter::get_post_sessions( $posts );
		$strength = self::convert_session_to_strength_point( $session_sum );

		update_user_meta( $author_id, 'strength', $strength );
	}

	/**
	 * Update defense.
	 *
	 * Defense is calculated from page views.
	 *
	 * @param int $author_id
	 * @param array $posts
	 * @return void
	 */
	public static function set_defense( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );
		$pv_sum = BLQU_Access_Counter::get_post_page_views( $posts );
		$defense = self::convert_pv_to_defense_point( $pv_sum );

		update_user_meta( $author_id, 'defense', $defense );
	}

	/**
	 * Update agility.
	 *
	 * Agility is calculated from power to lead.
	 *
	 * @param int $author_id
	 * @param array $posts
	 * @return void
	 */
	public static function set_agility( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );
		$power_to_lead_sum = BLQU_Access_Counter::get_posts_power_to_leads( $posts );
		$agility = self::convert_power_to_lead_to_agility_point( $power_to_lead_sum );

		update_user_meta( $author_id, 'agility', $agility );
	}

	/**
	 * Calculate Strength from sessions.
	 *
	 * @param int $session
	 * @return int
	 */
	private static function convert_session_to_strength_point( $session ) {
		return $session * 5;
	}

	/**
	 * Calculate Defense from page views.
	 *
	 * @param int $pv
	 * @return int
	 */
	private static function convert_pv_to_defense_point( $pv ) {
		return $pv * 4;
	}

	/**
	 * Calculate Agility from power to lead.
	 *
	 * @param int $power_to_lead
	 * @return int
	 */
	private static function convert_power_to_lead_to_agility_point( $power_to_lead ) {
		return $power_to_lead * 10;
	}

	/**
	 * Sort users in descending order of the parameter.
	 *
	 * @param string $parameter
	 * @param string $return
	 * @return array
	 */
	public static function get_characters_parameter_ranking( $parameter, $return = 'characters' ) {
		$characters = BLQU_Character::get_all_characters();
		$call_back_for_sort = function ( $a, $b ) use( $parameter ) {
			list( $ap, $bp ) = array(
				$a->status->$parameter,
				$b->status->$parameter,
			);
			if ( $ap == $bp ) return 0;
			return ( $ap < $bp ) ? +1 : -1;
		};
		usort( $characters, $call_back_for_sort );

		if ( $return == 'ids' ) {
			$character_ids = array();
			foreach ( $characters as $character ) {
				$character_ids[] = $character->id;
			}
			return $character_ids;
		}

		return $characters;
	}

	public static function get_characters_top_of_the_parameter( $parameter, $authors_number = 3 ) {
		$characters = self::get_characters_parameter_ranking( $parameter );
		return array_slice( $characters, 0, $authors_number );
	}

	/**
	 * Update all user's statuses.
	 *
	 * @return void
	 */
	public static function set_all_characters_status() {
		$args = array(
			'role__in' => array( 'author', 'administrator'),
		);
		$authors = get_users( $args );

		foreach ( $authors as $author ) {
			self::set_status( $author->ID );
		}
	}

	public static function get_author_posts( $author_id ) {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'post',
			'author'         => $author_id,
			'post_status'    => 'publish',
		);
		$posts = get_posts( $args );
		return $posts;
	}

	public function convert_array() {
		$array = array();
		foreach ( self::PARAMETERS as $parameter ) {
			$array[ $parameter ] = $this->$parameter;
		}
		return $array;
	}

}