<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Record {
	public const ITEMS = array(
		'posts_count',
		'words_count',
		'rewrite_count',
	);

	public $posts_count;
	public $words_count;
	public $rewrite_count;

	public function __construct( $author_id = null ) {
		if ( $author_id ) {
			$this->posts_count = self::get_posts_count( $author_id );
			$this->words_count = self::get_words_count( $author_id );
			$this->rewrite_count = self::get_rewrite_count( $author_id );
		}
	}

	public static function get_instance_from_array( $record_array = array() ) {
		$record = new self;
		foreach ( self::ITEMS as $item ) {
			$value = $record_array[ $item ];
			$record->$item = $value ? $value : 0;
		}
		return $record;
	}

	public static function add_action_for_record() {
		add_action( 'publish_post', array( __CLASS__, 'set_record_when_publish' ), 10, 2 );
		add_action( 'save_post', array( __CLASS__, 'set_record_after_save' ), 10, 2 );
	}

	public static function get_posts_count( $author_id ) {
		$posts_count = get_user_meta( $author_id, 'posts_count', true );
		return $posts_count ? (int) $posts_count : 0;
	}

	public static function get_words_count( $author_id ) {
		$words_count = get_user_meta( $author_id, 'words_count', true );
		return $words_count ? (int) $words_count : 0;
	}

	public static function get_rewrite_count( $author_id ) {
		$rewrite_count = get_user_meta( $author_id, 'rewrite_count', true );
		return $rewrite_count ? (int) $rewrite_count : 0;
	}

	public static function set_record( $author_id ) {
		$posts = self::get_author_posts( $author_id );
		self::set_posts_count( $author_id, $posts );
		self::set_words_count( $author_id, $posts );
		self::set_rewrite_count( $author_id, $posts );
	}

	public static function set_posts_count( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );
		$posts_count = count( $posts );
		update_user_meta( $author_id, 'posts_count', $posts_count );
	}

	public static function set_words_count( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );
		$words_count_sum = 0;

		foreach ( $posts as $post ) {
			$words_count = mb_strlen( strip_tags( $post->post_content ), 'UTF-8' );
			$words_count_sum += $words_count;
		}

		update_user_meta( $author_id, 'words_count', $words_count_sum );
	}

	/**
	 * After getting the number of rewrites for each article, calculate and update the number of rewrites for each user.
	 *
	 * @param int $author_id
	 * @param array $posts
	 * @return void
	 */
	public static function set_rewrite_count( $author_id, $posts = null ) {
		$posts = $posts ? $posts : self::get_author_posts( $author_id );

		$rewrite_count_sum = 0;

		foreach ( $posts as $post ) {
			$rewrite_count_str = get_post_meta( $post->ID, 'rewrite_count', true );
			$rewrite_count = $rewrite_count_str ? intval( $rewrite_count_str ) : 0;
			$rewrite_count_sum += $rewrite_count;
		}

		update_user_meta( $author_id, 'rewrite_count', $rewrite_count_sum );
	}

	/**
	 * Update record after save post.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @return void
	 */
	public static function set_record_after_save( $post_id, $post ) {
		if ( $post->post_status != "publish" ) {
			$author_id = $post->post_author;
			$posts = self::get_author_posts( $author_id );
			self::set_posts_count( $author_id, $posts );
			self::set_words_count( $author_id, $posts );
		}
	}

	/**
	 * Update number of rewrites and save back up posts when publish post.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @return void
	 */
	public function set_record_when_publish( $post_id, $post ) {
		self::set_rewrite_count_when_publish( $post );
		self::set_backup_post_content( $post );
	}

	public static function set_rewrite_count_when_publish( $post ) {
		$before_post_content = self::get_backup_post_content( $post );
		if ( $before_post_content && $before_post_content != $post->post_content ) {
			self::set_post_rewrite_count_when_publish( $post->ID );
			self::set_author_rewrite_count_when_publish( $post->post_author ); // 処理を早めるためにset_rewrite_countは未使用
		}
	}

	/**
	 * Update number of post rewrites.
	 *
	 * @param int $post_id
	 * @return void
	 */
	public static function set_post_rewrite_count_when_publish( $post_id ) {
		$post_rewrite_count = get_post_meta( $post_id, 'rewrite_count', true );
		$post_rewrite_count = $post_rewrite_count ? $post_rewrite_count + 1 : 1;
		update_post_meta( $post_id, 'rewrite_count', $post_rewrite_count );
	}

	/**
	 * Update total number of user articles rewritten.
	 *
	 * @param int $author_id
	 * @return void
	 */
	public static function set_author_rewrite_count_when_publish( $author_id ) {
		$author_rewrite_count = self::get_rewrite_count( $author_id );
		update_user_meta( $author_id, 'rewrite_count', $author_rewrite_count + 1 );
	}

	public static function get_backup_post_content( $post ) {
		$backup_content = get_post_meta( $post->ID, 'backup_content', true );
		return $backup_content ? $backup_content : '';
	}

	public static function set_backup_post_content( $post ) {
		update_post_meta( $post->ID, 'backup_content', $post->post_content );
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
		foreach ( self::ITEMS as $item ) {
			$array[ $item ] = $this->$item;
		}
		return $array;
	}

	public static function set_all_characters_record() {
		$args = array(
			'role__in' => array( 'author', 'administrator'),
		);
		$authors = get_users( $args );

		foreach ( $authors as $author ) {
			self::set_record( $author->ID );
		}
	}

}

BLQU_Record::add_action_for_record();