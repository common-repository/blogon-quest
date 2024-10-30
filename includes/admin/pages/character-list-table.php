<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists( 'WP_List_Table' ) ){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BLQU_Character_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct();
	}

	public function prepare_items() {
		$this->items = BLQU_Character::get_all_characters();
		$this->sort_items();
		$this->paginate();
	}

	public function get_columns() {
		return array(
			'name' => __( 'name', 'blogon-quest' ),
			'strength' => __( 'strength', 'blogon-quest' ),
			'defense' => __( 'defense', 'blogon-quest' ),
			'agility' => __( 'agility', 'blogon-quest' ),
			'posts_count' => __( 'posts count', 'blogon-quest' ),
			'word_count' => __( 'word count', 'blogon-quest' ),
			'number_of_rewriting' => __( 'rewrite times', 'blogon-quest' ),
		);
	}

	protected function get_sortable_columns() {
		return array(
			'strength' => array( 'strength', true ),
			'defense' => array( 'defense', true ),
			'agility' => array( 'agility', true ),
			'posts_count' => array( 'posts_count', true ),
			'word_count' => array( 'word_count', true ),
			'number_of_rewriting' => array( 'number_of_rewriting', true ),
		);
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name' : return $item->display_name;
			case 'strength' : return $item->status->strength;
			case 'defense' : return $item->status->defense;
			case 'agility' : return $item->status->agility;
			case 'posts_count' : return $item->record->posts_count;
			case 'word_count' : return $item->record->words_count;
			case 'number_of_rewriting' : return $item->record->rewrite_count;
		}
	}

	/**
	 * Sort items.
	 *
	 * @return void
	 */
	public function sort_items() {
		$getter_names = $this->getter_names_for_sort();
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
		$orderDir = isset( $_GET['order'] ) && sanitize_text_field( $_GET['order'] ) === 'asc' ? 1 : -1;
		$getter = isset( $getter_names[ $orderby ] ) ? $getter_names[ $orderby ] : null;
		$sort = function ( $a, $b, $bigA ) {
			if ( $a == $b ) return 0;
			return ( $a > $b ) ? +$bigA : -$bigA;
		};
		if ( $getter ) {
			usort(
				$this->items,
				function( $a, $b ) use( $getter, $sort, $orderDir ) {
					return $sort( $getter($a), $getter($b), $orderDir );
				}
			);
		}
	}

	/**
	 * Items to be sorted.
	 *
	 * @return array
	 */
	public function getter_names_for_sort() {
		return array(
			'strength' => function( $item ) { return $item->status->strength; },
			'defense' => function( $item ) { return $item->status->defense; },
			'agility' => function( $item ) { return $item->status->agility; },
			'posts_count' => function( $item ) { return $item->record->posts_count; },
			'word_count' => function( $item ) { return $item->record->words_count; },
			'number_of_rewriting' => function( $item ) { return $item->record->rewrite_count; },
		);
	}

	/**
	 * paginate
	 *
	 * @return void
	 */
	public function paginate() {
		$this->set_pagination_args([
			'total_items' => count($this->items),
			'per_page' => 10
		]);

		$paged = $this->get_pagenum();

		$per_page = $this->get_pagination_arg('per_page');

		$this->items = array_slice(
			$this->items,
			$per_page * ($paged - 1),
			$per_page
		);
	}

	public function render(){
		$this->prepare_items();
		?>
		<div class="character-list">
			<h1>BLOGON QUEST</h1>
			<h2>CHARACTER LIST</h2>
			<form id="movies-filter" method="get">
					<input type="hidden" name="page" value="<?php echo esc_html( $_REQUEST['page'] ) ?>" />
					<?php $this->display() ?>
			</form>
			<h3>Description</h3>
			<p>
			<?php if ( blqu_is_japanese() ) : ?>
				こうげきりょく、しゅびりょく、すばやさの3項目は、どれもライティングの成果に応じて値が決まります。<br>
				<br>
				こうげきりょくは外部サイトからライターの記事に訪れたユーザーの数を元に計算しています。<br>
				計算式: 外部サイトからライターの記事に訪れたユーザーの数 * 5<br>
				<br>
				しゅびりょくはライターの記事のPV数を元に計算しています。<br>
				計算式: 外部サイトからライターの記事に訪れたユーザーの数 * 4<br>
				<br>
				すばやさは記事の読者がサイト内の別ページに飛んだ回数を元に計算します。<br>
				計算式: 記事の読者がサイト内の別ページに飛んだ回数 * 10
			<?php else : ?>
				There are three categories of status: Strength, Defense, and Agility.<br>
				All of them have points based on the achievements of writing.<br>
				<br>
				Strength is calculated from the number of people who came to your article from an external site.<br>
				Formula: the number of people who came to your article from an external site * 5.<br>
				<br>
				Defense is calculated from page views for your posts.<br>
				Formula: page views for your posts * 4.<br>
				<br>
				Agility is calculated from the number of people who jumped to other pages on your site after reading one of your posts.<br>
				Formula: the number of people who jumped to other pages on your site after reading one of your posts * 10.<br>
			<?php endif; ?>
			</p>
		</div>
		<?php
	}
}