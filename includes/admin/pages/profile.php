<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Profile {

	public function __construct( $current_character = '' ) {
		$this->current_character = $current_character ? $current_character : BLQU_Character::get_current_character();
	}

	/**
	 * Show current user's profile.
	 *
	 * @return void
	 */
	public function html() {
	?>
		<div class="toggle-block profile-block main-block">
			<div class="status-window bq-window">
				<h3 class="window-title">STATUS</h3>
				<ul>
				<?php
				foreach ( BLQU_Status::PARAMETERS as $parameter_name ) :
					$parameter = $this->current_character->status->$parameter_name;
				?>
					<li class="status-item">
						<h4 class="<?php echo $parameter_name ?>-heading"><?php echo ucfirst( __( $parameter_name, 'blogon-quest' ) ) ?></h4>
						<p><span><?php echo $parameter ?></span>pt</p>
					</li>
				<?php
				endforeach;
				?>
				</ul>
			</div>
			<div class="record-window bq-window">
				<h3 class="window-title">RECORD</h3>
				<ul>
				<?php
				foreach ( BLQU_Record::ITEMS as $item_name ) :
					$record = $this->current_character->record->$item_name;
				?>
					<li class="record-item">
						<h4><?php echo ucfirst( __( $item_name, 'blogon-quest' ) ) ?></h4>
						<p><span><?php echo $record ?></span><?php echo __( "{$item_name}_unit", 'blogon-quest' ) ?></p>
					</li>
				<?php
				endforeach;
				?>
				</ul>
			</div>
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
		return $encourage_comments[0];
	}

	public function encourage_comments() {
		list( $parameter_name, $parameter ) = $this->current_character->get_top_of_parameter_name_and_value();
		switch ( $parameter_name ) {
			case 'strength':
				$suitable_role = "attacker";
				break;
			case 'defense':
				$suitable_role = "tank";
				break;
			case 'agility':
				$suitable_role = "supporter";
				break;
		}
		if ( blqu_is_japanese() ) {
			return array(
				__( $parameter_name, 'blogon-quest' ) . 'が高いきみは ' . __( $suitable_role, 'blogon-quest' ) . '向きだ！',
			);
		} else {
			return array(
				"With your high ${parameter_name}, you're a good ${suitable_role}!",
			);
		}
	}
}