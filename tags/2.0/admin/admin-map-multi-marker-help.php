<?php

global $mapmarker_info;

?>
<div class="wrap">

	<!-- Affiche le nom du plugin -->
	<h1><?php _e('Help', 'map-multi-marker'); echo ' '.$mapmarker_info['plugin_name'] ?></h1>
	<div class="notice notice-info is-dismissible">
		<p><?php _e('To use the plugin simply insert the shortcode', 'map-multi-marker'); echo ' <strong>['.$mapmarker_info['shortcode'].']</strong> '; _e('into post or page.', 'map-multi-marker'); ?></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button>
	</div>

	<button class="button button-primary" id="copy-shortcode"><?php _e('Click to copy the shortcode', 'map-multi-marker'); ?></button>


	<img src="<?php echo plugin_dir_url(__DIR__).'img/aide-plugin.jpg'; ?>" alt="Image d'aide pour le plugin" class="help-img img-responsive">

	<!-- Script pour init Clipboard.js -->
	<script type="text/javascript">
		var clipboard = new Clipboard('#copy-shortcode', {
		    text: function() {
		        return '<?php echo '['.$mapmarker_info['shortcode'].']';?>';
		    }
		});
	</script>
</div>