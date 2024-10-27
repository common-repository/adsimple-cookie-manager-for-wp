<?php
	/**
	 * The template to render block after install plugin.
	 *
	 * @var array $field
	 *
	 * @version 1.0.0
	 */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
?>
<div class="as_cm-update-page as_cm-after-install-page">
    <!--UPDATE BANNER-->
    <div class="as_cm-update-banner as_cm-update-page__banner">
        <div class="as_cm-update-banner__logo"></div>
        <div class="as_cm-update-banner__img-wrap">
            <div class="as_cm-update-banner__img">
                <div class="as_cm-update-banner__img-inner"></div>
            </div>
        </div>
    </div>
    <!--UPDATE BANNER-->
    <!--UPDATE CONTENT-->
    <div class="as_cm-update-content as_cm-update-page__content">
        <form action="<?= AS_CM_Controllers_Options::get_page_url(); ?>" method="post">
            <!--MAIN-->
            <div class="as_cm-update-content__inner">
                <h2 class="as_cm-update-content__title"><?= sprintf( __( 'You use AdSimple® Consent Manager %s!',
				                                                         AS_CM_Manager::$action ),
				                                                     '<span>2.0</span>' ); ?></h2>
                <p class="as_cm-update-content__subtitle">
					<?= sprintf( __( 'Please enter your AdSimple® ID, you can get it on %s.', AS_CM_Manager::$action ),
					             '<a href="' . AS_CM_Services_REST::get_prepared_url_based_on_environment( AS_CM_Services_REST::LINK_TO_COOKIE_MANAGER_SERVICE ) . '" target="_blank" class="as_cm-update-content__subtitle-span">adsimple.at</a>' ); ?>
                </p>
                <div class="as_cm-update-content__formpos">
                    <input autocomplete="off" required type="text"
                           name="<?= $field['name']; ?>"
                           value="<?= AS_CM_Controllers_Options::get_option( $field['key'] ); ?>"
                           class="as_cm-adsimple-id as_cm-form__input"/>
                    <div class="as_cm-update-content__button">
                        <button type="submit" class="as_cm-btn as_cm-btn--orange">
							<?= AS_CM_Controllers_Options::is_configured() ? __( 'Save', AS_CM_Manager::$action ) : __( 'Set ID', AS_CM_Manager::$action ); ?>
                        </button>
                    </div>
                </div>
                <div class="as_cm-update-content__notice">
                    <?= AS_CM_Helpers_View::get_template_part( 'options/cache' );?>
                    <p class="as_cm-update-content__notice-header"><strong><?= __( 'Cookie Overview', AS_CM_Manager::$action );?></strong></p>
                    <p><?= sprintf( __( 'Use the shortcode %s to show an ordered list of all cookies on a subpage of your website..', AS_CM_Manager::$action ), '[' . AS_CM_Controllers_Shortcodes::SHORTCODE_COOKIE_LIST . ']' );?></p>
                    <br/>
                    <p>
						<?= sprintf( __( 'For include popup to site uses hook %s. You theme should have support of this hook.',
						                 AS_CM_Manager::$action ),
						             '<a href="https://developer.wordpress.org/reference/hooks/wp_head/" target="_blank"><strong>wp_head</strong></a>' ); ?>
                    </p>
                    <p>
						<?= sprintf( __( 'Current version of plugin %s.', AS_CM_Manager::$action ),
						             AS_CM_Manager::$version ); ?>
                    </p>
                </div>
            </div>
            <!--/MAIN-->

        </form>
    </div>
    <!--/UPDATE CONTENT-->
</div>