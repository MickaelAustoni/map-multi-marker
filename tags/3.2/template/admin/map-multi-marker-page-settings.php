<?php
/**
 * @var $language
 * @var $api_key
 */
?>

<div class="wrap">
    
    <?php if (empty($this->api_key) || $this->api_key === MapMultiMarker::DEFAULT_API_KEY): ?>
        <div class="error notice">
            <p><strong><?php echo __('Now, before you start using Map Multi Marker, please note that it is necessary to register your API key', MapMultiMarker::TEXT_DOMAIN
                        ) . ' ' . __('to work properly.', MapMultiMarker::TEXT_DOMAIN
                        ) ?></strong></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <h1><?php _e('Google API', MapMultiMarker::TEXT_DOMAIN); ?></h1>
        <input type="hidden" name="form_settings" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="language"><?php _e('Map language', MapMultiMarker::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input id="language" type="text" name="language" value="<?php echo $this->language ?>">
                    <p class="description" id="tagline-description"><?php _e('(English = en) , (French=fr) , (Russian=ru) , ...', MapMultiMarker::TEXT_DOMAIN) ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="apikey"><?php _e('Google API key', MapMultiMarker::TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input id="apikey" type="text" name="apikey" value="<?php echo esc_html($this->api_key) ?>">
                    <p class="description">
                        <a target="_blank" href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Celevation_backend%2Cplaces_backend&reusekey=true&hl=<?php echo $this->language; ?>"><?php _e('Get a google API key',
                                MapMultiMarker::TEXT_DOMAIN
                            ) ?></a>
                        <br>
                        <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=<?php echo $this->language ?>"><?php _e('Help', MapMultiMarker::TEXT_DOMAIN
                            ) ?></a>
                    </p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" name="save_g_api" id="save_g_api" class="button button-primary">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> <?php _e('Save', MapMultiMarker::TEXT_DOMAIN) ?>
            </button>

        </p>
    </form>
</div>