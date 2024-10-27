<?php
	/**
	 * The template to render wrapper for block settings.
	 *
	 * @version 1.0.0
	 *
	 */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
?>
<div class="as_cm-wrapper" id="as_cm-wrap">
	<?php do_action( AS_CM_Manager::$action.'_render_dashboard_options' ); ?>
</div>