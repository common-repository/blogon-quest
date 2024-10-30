<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Menu {
	private static $instance;

	private $slug = 'blogon-quest';
	private $capability = 'publish_posts';
	private $blqu_character_list_table;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function add_menu() {
		$this->blqu_character_list_table = new BLQU_Character_List_Table();

		add_menu_page(
			'BLOGON QUEST',
			'BLOGON QUEST',
			$this->capability,
			$this->slug,
			array( $this, 'home_page' ),
			'dashicons-games',
			'25.311',
		);

		add_submenu_page(
			$this->slug,
			'BLOGON QUEST',
			'Profile',
			$this->capability,
			$this->slug,
			'',
		);

		add_submenu_page(
			$this->slug,
			'BLOGON QUEST character List',
			'Character List',
			'manage_options',
			$this->slug . '-character-list',
			array( $this, 'character_list_page' ),
		);
	}

	public function home_page() {
		BLQU_Home::display();
	}

	public function character_list_page() {
    $this->blqu_character_list_table->render();
	}
}

$blqu_menu = BLQU_Menu::get_instance();