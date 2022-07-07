<?php
global $mapmarker_info;
checkPersonalApiKey();
?>
<div class="wrap">
    <!-- Affiche le nom du plugin -->
    <h1><?php _e('Help', 'map-multi-marker');
        echo ' ' . $mapmarker_info[ 'plugin_name' ] ?></h1>
    <p></p>
    <div class="wrap-help-mmm">
        <h2><?php _e('To use the plugin simply insert your shortcode into post, page or widget.', 'map-multi-marker'); ?></h2>
        <img src="<?php echo plugin_dir_url(__DIR__) . 'img/aide-plugin.jpg'; ?>" alt="Image d'aide pour le plugin"
             class="help-img img-responsive">
    </div>

    <hr>

    <div class="wrap-help-mmm">
        <h2><?php _e('Example of CSV file separated by ";".', 'map-multi-marker'); ?></h2>
        <img src="<?php echo plugin_dir_url(__DIR__) . 'img/help-csv-v2.jpg'; ?>" alt="Image d'aide pour le plugin"
             class="help-img img-responsive">
    </div>

</div>