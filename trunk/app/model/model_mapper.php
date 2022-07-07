<?php

require_once MapMultiMarker::instance()->plugin_path . 'app/model/model.php';

use map_multi_marker\model\model;

class model_mapper extends model
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    //======================================================================
    // SETTINGS
    //======================================================================

    /**
     * Get setting
     */
    public function get_api_setting()
    {

        $setting_api = $this->wpdb->get_results("SELECT * FROM " . $this->wpdb->prefix . 'mapmarker_api', ARRAY_A);

        return [
            'api_key' => $setting_api[0]['apikey'],
            'language' => $setting_api[0]['language']
        ];
    }

    /**
     * Save setting
     *
     * @param model $modelInstance
     *
     * @return false|int
     */
    public function save_api_settings(model $modelInstance)
    {

        $query = $this->wpdb->update($this->wpdb->prefix . 'mapmarker_api', [
            'language' => $modelInstance->get_language(),
            'apikey' => $modelInstance->get_api_key()
        ], [
                'id' => '1'
            ]
        );

        return $query;
    }

    //======================================================================
    // OPTIONS
    //======================================================================

    /**
     * Get map option
     *
     * @param $mapId
     *
     * @return mixed
     */
    public function get_map_options($mapId)
    {
        $setting_api = $this->wpdb->get_row('SELECT * FROM ' . $this->wpdb->prefix . 'mapmarker_option WHERE map_id=' . $mapId, ARRAY_A);

        return $setting_api;
    }

    /**
     * Return all map id
     *
     * @return array|object|null
     */
    public function get_map_all_id()
    {
        return $this->wpdb->get_results("SELECT map_id FROM " . $this->wpdb->prefix . 'mapmarker_option', ARRAY_A);
    }


    /**
     * Save options
     */
    public function save_options()
    {

    }

    //======================================================================
    // MARKERS
    //======================================================================

    public function get_google_map_marker($mapId)
    {

        $markers = $this->wpdb->get_results('SELECT id, latitude, longitude, img_icon_marker FROM ' . $this->wpdb->prefix . 'mapmarker_marker WHERE marker_id=' . $mapId, ARRAY_A
        );

        return $markers;
    }

    /**
     * Save markers
     */
    public function save_markers()
    {

    }

}