<?php
	/**
	 * The template to render cache block.
	 *
	 * @version 2.0.6
	 */
	
	if( ! defined( 'ABSPATH' ) )
	{
		exit;
	}
	
	if( !AS_CM_Controllers_Options::is_configured() ){
	    return;
    }
	
	$cache = AS_CM_Controllers_Options::get_option('cache');
	
	$disable = !AS_CM_Services_Cache_Loader::is_cache_dir_available() ? 'disabled' : '';
	$clear_cache_url = AS_CM_Services_Cache_Manager::get_clear_cache_url();
?>
<div class="as_cm-container-clear-cache">
    <p>
        <label class="as_cm-clear-cache-checkbox">
            <span class="as_cm-clear-cache-checkbox__box <?= !empty( $disable ) ? 'as_cm-clear-cache-checkbox__box--is-disabled' : '';?>">
                <input <?= $disable;?> class="as_cm-clear-cache-checkbox__input" type="checkbox" value="1" name="<?= AS_CM_Helpers_General::prepare_name( 'cache_available' );?>" <?= $cache['available'] ? 'checked' : '';?>>
                <svg class="as_cm-clear-cache-checkbox__figure" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="img" viewBox="0 0 512 512"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg>
            </span>
            <span class="as_cm-clear-cache-checkbox__title"><?= __( 'Save a local copy of the script and clear it every', AS_CM_Manager::$action );?></span>
        </label>
        <input <?= $disable;?> class="as_cm-clear-cache-input" type="number" step="1" min="0" name="<?= AS_CM_Helpers_General::prepare_name( 'cache_period_value' );?>" value="<?= $cache['period_value'];?>">
        <select <?= $disable;?> class="as_cm-clear-cache-select" name="<?= AS_CM_Helpers_General::prepare_name( 'cache_period_type' );?>">
            <?php foreach (AS_CM_Controllers_Options::get_available_period_types() as $type => $label):?>
                <option value="<?= $type;?>" <?= $type == $cache['period_type'] ? 'selected' : '';?>><?= $label;?></option>
            <?php endforeach;?>
        </select>
        <?php if( !empty( $clear_cache_url ) ):?>
            <?= __( 'OR', AS_CM_Manager::$action );?> <a href="<?= $clear_cache_url;?>"><?= __( 'clear the cache manually', AS_CM_Manager::$action );?></a>
        <?php endif;?>
    </p>
</div>
<?php if( !AS_CM_Services_Cache_Loader::is_cache_dir_available() ):?>
    <p class="as_cm-update-content__notice-error"><?= __( "We can't save the cache locally because your site doesn't allow writing files.", AS_CM_Manager::$action );?></p>
<?php endif; ?>
<p class="as_cm-update-content__notice-header"><strong><?= __( 'Local cache', AS_CM_Manager::$action );?></strong></p>
<p><?= __( "If you choose to install the script locally then it won't be necessary to request the script from our servers on every load. The activation of this option will increase the speed of your site. But if you decide to change the styling or some texts within your user account on www.adsimple.at then you have to manually clear the cache here to actually see the changes on your website.", AS_CM_Manager::$action );?></p>