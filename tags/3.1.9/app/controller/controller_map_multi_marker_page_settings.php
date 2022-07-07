<?php

require_once MapMultiMarker::instance()->plugin_path . 'app/controller/controller_map_multi_marker.php';
require_once MapMultiMarker::instance()->plugin_path . 'app/model/model_mapper.php';

class controller_map_multi_marker_page_settings extends controller_map_multi_marker
{
    /**
     * @var
     */
    private $model;
    /**
     * @var
     */
    public $settings;
    /**
     * @var bool|string
     */
    public $language;
    /**
     * @var string
     */
    public $api_key;

    /**
     * controller_map_multi_marker_page_help constructor.
     */
    public function __construct()
    {

        $this->model = new model_mapper();

        if (!empty($_POST['form_settings'])) {
            $this->post_form_settings();
        }

        $this->load_form_settings();
        $this->loadView();

    }

    /**
     * Load form data
     */
    public function load_form_settings()
    {
        $this->settings = $this->model->get_api_setting();
        $this->language = $this->settings['language'];
        $this->api_key = $this->settings['api_key'];
    }

    /**
     * Post form data
     */
    private function post_form_settings()
    {
        # Check nonce
        if (wp_verify_nonce($_POST['form_settings'], 'securite-nonce')) {

            // Sanitize
            $api_key = sanitize_text_field($_POST['apikey']);
            $language = sanitize_text_field($_POST['language']);

            $this->model->set_language($language);
            $this->model->set_api_key($api_key);
            $this->model->save_api_settings($this->model);

            # Display alert
            MapMultiMarker::instance()->mmm_alert_message(__('Your changes have been saved.', 'map-multi-marker'), 'updated');

            return true;

        } else {
            # Display error
            MapMultiMarker::instance()->mmm_alert_message(__('Error in the form.', 'map-multi-marker'), 'error');
            exit;
        }
    }

}