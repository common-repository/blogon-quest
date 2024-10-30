<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BLQU_Help {
	/**
	 * Show what is BLOGON QUEST.
	 *
	 * @return void
	 */
	public function html() {
?>
<div class="help-block">
	<div class="help-content">
		<h2 class="blogon-quest-subtitle">HELP</h2>
		<main class="help">
		<?php if( blqu_is_japanese() ) : ?>
			<h3>BLOGON QUESTとは？</h3>
			<p>日常のライティング活動をRPG化して、ゲーム感覚で楽しく執筆できるようにするプラグインだ。</p>
			<h3>STATUSについて</h3>
			<p>「<span class="strength">こうげきりょく</span>」、「<span class="defense">しゅびりょく</span>」、「<span class="agility">すばやさ</span>」の3項目があり、どれもライティングの成果に応じて値が決まるよ。<br>
				<span class="strength">こうげきりょく</span>は<strong>外部サイトからきみの記事に来た人の数</strong>、<span class="defense">しゅびりょく</span>は<strong>きみの記事のPV数</strong>、<span class="agility">すばやさ</span>は<strong>きみの記事の読者がサイト内の別ページに飛んだ回数</strong>を元に計算しているんだ。
			</p>
			<h3>RECORDについて</h3>
			<p>「投稿数」、「総文字数」、「リライト数」の3項目が記録されるよ。</p>
			<h3>RANKINGについて</h3>
			<p>ライターが何人もいる場合は、ライター間でのSTATUS順位が項目ごとに確認できるよ。<br>
				だけど上位3名しか表示されないから、名前をランキングに乗せたい人は頑張って記事を書こう！
			</p>
		<?php else : ?>
			<h3>What is BLOGON QUEST？</h3>
			<p>It's a plugin that turns your daily writing activities into an RPG and makes writing articles fun just like playing game.</p>
			<h3>About Status</h3>
			<p>There are three categories of status: <span class="strength">Strength</span>, <span class="defense">Defense</span>, and <span class="agility">Agility</span>.<br>
				All of them have points based on the achievements of writing.<br>
				<span class="strength">Strength</span> is calculated from the number of people who came to your article from an external site.<br>
				<span class="defense">Defense</span> is calculated from page views for your posts.<br>
				<span class="agility">Agility</span> is calculated from the number of people who jumped to other pages on your site after reading one of your posts.
			</p>
			<h3>About Record</h3>
			<p>There are three categories of status: posts count, word count, and rewrite times.</p>
			<h3>About Ranking</h3>
			<p>If you have multiple writers, you can check the ranking of 'STATUS' among writers for each item.<br>
				However, the ranking shows only the top three names of them, so if you want to get your name in it, do your best to write articles!
			</p>
		<?php endif; ?>
		</main>
		<div class="help-footer">
			<div id="help-close-button">CLOSE</div>
		</div>
	</div>
</div>
<script>
	let helpButtonFlash;
	let isHelpButtonFlashEvent = false;
	let scrollTopWhenHelp;
	const $helpBlock = jQuery('.help-block');
	const $helpContent = jQuery('.help-content');
	const $helpButton = jQuery('#help-button');
	const $wpContent = jQuery('#wpcontent');
	const helpBlockHeight = $helpBlock.innerHeight();
	$helpBlock.css({top: `-${helpBlockHeight}px`});
	$helpButton.on('click', (e) => {
		e.preventDefault();
		<?php if ( wp_is_mobile() ) : ?>
			const contentHeight = $wpContent.innerHeight();
		<?php endif; ?>
		$helpBlock.addClass('frame-in');
		isHelpButtonFlashEvent = true;
		helpButtonFlash = setInterval(helpButtonSwitchDisplay, 600);
		<?php if ( wp_is_mobile() ) : ?>
			const adminBarHeight = jQuery('#wpadminbar').innerHeight();
			scrollTopWhenHelp = window.scrollY;
			$wpContent.css({top: `-${scrollTopWhenHelp}px`, height: contentHeight});
			$helpBlock.css({marginTop: scrollTopWhenHelp < adminBarHeight ? window.scrollY + adminBarHeight + 'px' : window.scrollY + 'px'});
			$helpContent.css({height: `calc(100vh - 80px - ${scrollTopWhenHelp < adminBarHeight ? adminBarHeight : 0}px)`});
		<?php endif; ?>
		$wpContent.addClass('fixed');
	})
	const $helpCloseButton = jQuery('#help-close-button');
	$helpCloseButton.on('click', (e) => {
		clearInterval(helpButtonFlash);
		isHelpButtonFlashEvent = false;
		$helpBlock.removeClass('frame-in');
		<?php if ( wp_is_mobile() ) : ?>
			$wpContent.css({top: '', height: ''});
			$helpBlock.css({marginTop: ''});
		<?php endif; ?>
		$wpContent.removeClass('fixed');
		window.scrollTo(0, scrollTopWhenHelp);
	})

	let isHelpButtonDisplay = true;
	const helpButtonSwitchDisplay = () => {
		if ( isHelpButtonDisplay ) {
			$helpCloseButton.removeClass('hide-button');
			isHelpButtonDisplay = false;
		} else {
			$helpCloseButton.addClass('hide-button');
			isHelpButtonDisplay = true;
		}
  }

	$helpCloseButton.on('mouseover', () => {
		if ( isHelpButtonFlashEvent ) {
			clearInterval(helpButtonFlash);
			$helpCloseButton.removeClass('hide-button');
		}
	}).on('mouseout', () => {
		if ( isHelpButtonFlashEvent ) {
			isHelpButtonDisplay = true;
			$helpCloseButton.addClass('hide-button');
			helpButtonFlash = setInterval(helpButtonSwitchDisplay, 600);
		}
	})
</script>
<?php
	}
}