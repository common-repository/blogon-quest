<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Ranking {
	/**
	 * Whether or not current user is listed in any of the rankings.
	 *
	 * @var boolean
	 */
	public $is_current_character_ranked;

	public function __construct( $current_character = '' ) {
		$this->current_character = $current_character ? $current_character : BLQU_Character::get_current_character();
		$this->is_current_character_ranked = false;
	}

	/**
	 * Show the best three users each parameter.
	 *
	 * @return void
	 */
	public function html() {
		?>
		<div class="toggle-block ranking-block main-block hide-block">
		<?php
		foreach ( BLQU_Status::PARAMETERS as $parameter_name ) :
			$top_characters = BLQU_Status::get_characters_top_of_the_parameter( $parameter_name );
		?>
			<div class="ranking-window bq-window">
				<h3 class="window-title <?php echo $parameter_name ?>-heading"><?php echo ucfirst( __( $parameter_name, 'blogon-quest' ) ) ?></h3>
				<ul>
				<?php
				foreach ( $top_characters as $rank => $top_character ) :
					$rank += 1;
					$is_current_character = $top_character->id == $this->current_character->id;
					if ( $is_current_character ) $this->is_current_character_ranked = true;
				?>
					<li>
						<h4 class="rank <?php echo "rank-" . $rank ?>"><?php echo $rank . blqu_get_ordinal_number( $rank ) ?></h4>
						<div class="<?php if ( $is_current_character ) echo "is-current-character" ?>">
							<h4 class="character"><?php echo $top_character->name ?></h4>
							<p class="parameter"><span><?php echo $top_character->status->$parameter_name ?></span>pt</p>
						</div>
					</li>
				<?php
				endforeach;
				?>
				</ul>
			</div>
		<?php
		endforeach;
		?>
		</div>
		<?php
	}

	/**
	 * Comment to encourage current user.
	 *
	 * @return string
	 */
	public function show_encourage_comment() {
		$encourage_comments = $this->encourage_comments();
		return $encourage_comments[ rand(0, 2) ];
	}

	public function encourage_comments() {
		if ( $this->is_current_character_ranked ) {
			return array(
				__( "You are in the top three.<br>Congratulations!!", 'blogon-quest' ),
				__( "You're in the ranks.<br>That's great!", 'blogon-quest' ),
				__( "Good job!<br>You are one of the best writers on this blog.", 'blogon-quest' ),
			);
		} else {
			return array(
				__( "Write more until you're ranked!!", 'blogon-quest' ),
				__( "One day your name will be on these rankings!", 'blogon-quest' ),
				__( "Go for it!!<br>You'll be on these lists.", 'blogon-quest' ),
			);
		}
	}
}