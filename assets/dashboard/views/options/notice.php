<?php
	/**
	 * The template to render notice block.
	 *
	 * @global boolean $is_ajax
	 * @global string  $text
	 * @global array   $link
	 * @global string  $dismiss_url
	 * @global string  $class
	 *
	 * @version 1.0.0
	 */
	
	if( ! defined( 'ABSPATH' ) )
	{
		exit;
	}
?>
<!--NOTICE-->
<div class="as_cm-notice js-as_cm-notice <?= isset( $is_ajax ) && $is_ajax ? 'js-as_cm-notice-ajax' : ''; ?> <?= isset( $class ) ? $class : '';?>" style="display:none;">
	<div class="as_cm-notice__logo"></div>
	<div class="as_cm-notice__message js-as_cm-notice-text">
    <span class="as_cm-notice__message-text">
      <?= $text; ?>
    </span>
	</div>
	<?php if( ( isset( $link['href'] ) && isset( $link['text'] ) ) || ( isset( $dismiss ) && $dismiss ) ): ?>
		<div class="as_cm-notice__button-block js-as_cm-notice-actions">
			<?php if( isset( $link['href'] ) && isset( $link['text'] ) ): ?>
				<a
					href="<?= $link['href'];?>"
                    <?= isset( $link['target'] ) ? sprintf( 'target="%s"', $link['target'] ) : '';?>
					class="as_cm-notice__button as_cm-notice__button_upload js-as_cm-notice-actions-button"
					data-confirm="<?= isset( $confirm ) && $confirm !== FALSE ? ( $confirm === TRUE ? __( 'Are you sure?', AS_CM_Manager::$action ) : $confirm ) : '';?>"
				>
					<?= $link['text'];?>
					<span class="as_cm-notice__button-icon-group">
                    <i class="as_cm-notice__button-icon-send"></i>
                </span>
				</a>
			<?php endif;?>
			<?= isset( $dismiss ) && $dismiss ? '<a href="#" class="as_cm-notice__button as_cm-notice__button_dismiss js-as_cm-notice-actions-dismiss" data-dismiss-url="'.$dismiss_url.'">'.__( 'DISMISS', AS_CM_Manager::$action ).'</a>' : '';?>
		</div>
	<?php endif; ?>
</div>
<!--/NOTICE-->
