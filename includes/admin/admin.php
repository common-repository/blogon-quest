<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Admin {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
	}

	/**
	 * load css for BLOGON QUEST page.
	 *
	 * @return void
	 */
	public function load_admin_style() {
		wp_enqueue_style( 'admin-status-style', blqu_get_stylesheet_url( 'admin/admin.css' ), array(), '', false );
	}
}

$blqu_admin = new BLQU_Admin();