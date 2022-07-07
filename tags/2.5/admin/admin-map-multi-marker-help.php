<?php

global $mapmarker_info;

?>
<div class="wrap">
	<!-- Affiche le nom du plugin -->
	<h1><?php _e('Help', 'map-multi-marker'); echo ' '.$mapmarker_info['plugin_name'] ?></h1>
	<p></p>
	<div class="row">
		<div class="col-md-6">
			<img src="<?php echo plugin_dir_url(__DIR__).'img/aide-plugin.jpg'; ?>" alt="Image d'aide pour le plugin" class="help-img img-responsive">
		</div>
		<div class="col-md-6">
			<h1 style="text-align: center;padding: 50px"><i class="fa fa-quote-left" aria-hidden="true"></i> <?php _e('To use the plugin simply insert your shortcode into post or page.', 'map-multi-marker'); ?> <i class="fa fa-quote-right" aria-hidden="true"></i></h1>
		</div>
	</div>
</div>