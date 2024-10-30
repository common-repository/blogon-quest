<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Dashboard {
	private static $instance;

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ));
	}

	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function add_dashboard_widgets() {
		if ( current_user_can( 'author' ) || current_user_can( 'administrator' ) ) {
			wp_add_dashboard_widget( 'blogon-quest-widget', 'BLOGON QUEST', array( $this, 'display_simple_info' ) );
		}
	}

	public function display_simple_info() {
		$character = BLQU_Character::get_current_character();
?>
<ul>
	<li>
	<?php if ( blqu_is_japanese() ) : ?>
		<p class="sign-in-days">きみがぼうけんにでてから<span><?php echo $character->sign_in_days_count ?>にち</span>！</p>
	<?php else : ?>
		<p class="sign-in-days">It's been <span><?php echo $character->sign_in_days_count ?> days</span> since you went on your adventure.</p>
	<?php endif; ?>
	</li>
	<?php
	$differences_parameters_count = count( BLQU_Status::PARAMETERS );
	foreach ( BLQU_Status::PARAMETERS as $parameter ) :
		$difference = $character->calculate_difference_current_parameter_between_last_parameter( $parameter );
		if ( $difference == 0 ) {
			$differences_parameters_count--;
			continue;
		}
	?>
		<li>
		<?php if ( blqu_is_japanese() ) : ?>
			<p><span class="<?php echo $parameter ?>-heading"><?php echo __( $parameter, 'blogon-quest' ) ?></span>が<strong class="<?php echo $difference > 0 ? "plus" : "minus" ?>"><?php echo abs( $difference ) ?>pt</strong><?php echo $difference > 0 ? "あがった！" : "へった..." ?>（<?php echo $character->last_status->$parameter ?>pt → <?php echo $character->status->$parameter ?>pt）</p>
		<?php else : ?>
			<p><span class="<?php echo $parameter ?>-heading"><?php echo $parameter ?></span> is <?php echo $difference > 0 ? "increased" : "decreased" ?> <strong class="<?php echo $difference > 0 ? "plus" : "minus" ?>"><?php echo abs( $difference ) ?>pt</strong>!（<?php echo $character->last_status->$parameter ?>pt → <?php echo $character->status->$parameter ?>pt）</p>
		<?php endif; ?>
		</li>
	<?php
	endforeach;
	if ( $differences_parameters_count == 0 ) :
		?>
		<li><?php echo __( 'Status did not change  :_(', 'blogon-quest') ?></li>
		<?php
	endif;
	?>
</ul>
<div>
	<p class="profile-link"><a href="/wp-admin/admin.php?page=blogon-quest">Go to PROFILE</a></p>
</div>

<?php
	}
}

$blqu_dashboard = BLQU_Dashboard::get_instance();