<?php

global $mapmarker_info;

?>
<div class="wrap">
	<!-- Affiche le nom du plugin -->
	<h1><?php _e('Help', 'map-multi-marker'); echo ' '.$mapmarker_info['plugin_name'] ?></h1>
	<p></p>
	<div class="notice notice-info is-dismissible">
		<p><?php _e('To use the plugin simply insert your shortcode into post or page.', 'map-multi-marker'); ?></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button>
	</div>

	<img src="<?php echo plugin_dir_url(__DIR__).'img/aide-plugin.jpg'; ?>" alt="Image d'aide pour le plugin" class="help-img img-responsive">
</div>