<?php
	/**
	 * JS for adding button to tinymce
	 *
	 * @var  string  $key
	 * @var  string  $icon
     * @var  string  $shortcode
	 *
	 * @version 1.0.0
	 */
	
	if( ! defined( 'ABSPATH' ) )
	{
		exit;
	}
?>
(function() {
	tinymce.PluginManager.add( '<?= $key;?>', function( editor, url ) {
		editor.addButton( '<?= $key;?>', {
		icon: '<?= $icon;?>',
		type: 'button',
        onclick: function() {
            editor.insertContent( '[<?= $shortcode;?>]' );
        }
		});
	});
})();