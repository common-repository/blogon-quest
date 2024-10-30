<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Home {
	public $is_current_character_ranked;
	public $profile;
	public $ranking;
	public $help;

	public function __construct() {
		$current_character = BLQU_Character::get_current_character();
		$this->is_current_character_ranked = false;
		$this->profile = new BLQU_Profile( $current_character );
		$this->ranking = new BLQU_Ranking( $current_character );
		$this->help = new BLQU_Help;
	}

	public static function display() {
		$instance = new self();
		$instance->html();
	}

	/**
	 * Create BLOGON QUEST page.
	 *
	 * @return void
	 */
	public function html() {
?>
<script>
	const content = document.getElementById('wpcontent');
	const backImage = '<?php echo $this->back_image_url() ?>';
	content.style.backgroundImage = `url('${backImage}')`;
</script>
<div class="blogon-quest-zone">
	<h1 class="blogon-quest-title">BLOGON QUEST</h1>
	<div class="toggle-block profile-block">
		<h2 class="blogon-quest-subtitle">PROFILE</h2>
	</div>
	<div class="toggle-block ranking-block hide-block">
		<h2 class="blogon-quest-subtitle">RANKING</h2>
	</div>
	<main>
		<div class="main-top">
			<div class="sidebar">
				<div class="menu-window bq-window">
					<ul>
						<li><a class="menu-button active" id="status-button" href=".profile-block"><?php echo strtoupper( __( "profile", 'blogon-quest' ) ) ?></a></li>
						<li><a class="menu-button" id="ranking-button" href=".ranking-block"><?php echo strtoupper( __( "ranking", 'blogon-quest' ) ) ?></a></li>
						<li><a class="menu-button" id="help-button" href=".help-block"><?php echo strtoupper( __( "help", 'blogon-quest' ) ) ?></a></li>
					</ul>
				</div>
			</div>
			<?php echo $this->profile->html() ?>
			<?php echo $this->ranking->html() ?>
		</div>
		<div class="main-bottom">
			<div class="free-text-window bq-window">
				<p class="toggle-block profile-block"><?php echo $this->profile->show_encourage_comment() ?></p>
				<p class="toggle-block ranking-block hide-block"><?php echo $this->ranking->show_encourage_comment() ?></p>
			</div>
		</div>
	</main>
</div>
<?php echo $this->help->html() ?>
<script>
	const menuButtons = jQuery('.menu-button:not(#help-button)');
	menuButtons.on('click', (e) => {
		e.preventDefault();
		menuButtons.removeClass('active');
		const menuButton = jQuery(e.target);
		menuButton.addClass('active');
		const targetBlockSelector = menuButton.attr('href');
		jQuery('.toggle-block').addClass('hide-block');
		jQuery(targetBlockSelector).removeClass('hide-block');
	})
</script>

<?php
	}

	public function back_image_url() {
		$hour = (int) wp_date('H');

		if ( $hour >= 6 && $hour < 16 ) {
			$back_image = blqu_get_image_url( 'river-morning.png' );
		} elseif ( $hour >= 16 && $hour < 19 ) {
			$back_image = blqu_get_image_url( 'road-evening.png' );
		} elseif ( $hour >= 19 || $hour < 6 ) {
			$back_image = blqu_get_image_url( 'river-night.png' );
		}

		return $back_image;
	}
}