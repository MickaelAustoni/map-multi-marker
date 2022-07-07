<?php

require_once MapMultiMarker::instance()->plugin_path . 'app/controller/controller_map_multi_marker.php';
require_once MapMultiMarker::instance()->plugin_path . 'app/model/model_mapper.php';
require_once MapMultiMarker::instance()->plugin_path . 'app/utility/map_multi_marker_utility.php';
require_once MapMultiMarker::instance()->plugin_path . 'app/utility/map_multi_marker_csv_reader.php';

class controller_map_multi_marker_page_manage extends controller_map_multi_marker
{

    private $model;
    public $isCreateMap;
    public $isEditMap;
    public $currentMapId;
    public $allMapId;
    public $apiSetting;

    /**
     * controller_map_multi_marker_page_help constructor.
     */
    public function __construct()
    {
        $this->currentMapId = isset($_GET['map_id']) ? $_GET['map_id'] : 0;
        $this->isEditMap = isset($_GET['edit_map']);
        $this->isCreateMap = isset($_GET['create_map']);
        $this->model = new model_mapper();
        $this->apiSetting = $this->model->get_api_setting();

        // Create map
        if ($this->isCreateMap) {
            $get_map_id = $this->mmm_get_all_map_id();
            $arr_map_id = [];

            foreach ($get_map_id as $result) {
                $arr_map_id[] = $result['map_id'];
            }

            if (!in_array($this->currentMapId, $arr_map_id)) {
                $this->mmm_create_new_map();
            }
        }

        $this->loadView();
    }

    /**
     * Get new map id
     */
    function mmm_get_new_id()
    {
        global $wpdb;

        //Req. sql
        $map_id = $wpdb->get_results("SELECT map_id FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

        //Si il n'y a plus aucune map de crée, alors la next map crée aura une valeur de "1"
        if (empty($map_id[0]['map_id'])) {
            echo "1";
        } //Sinon on incremente de "1" l'id de map le plus elever
        else {
            //Déclare l'array
            $highest_id = [];

            //Récup les valeurs de map_id du tableau multidimensionel
            foreach ($map_id as $value) {
                $highest_id[] = $value['map_id'];
            }

            //Return le résultat et l'incrémente de 1
            echo $highest_id = max($highest_id) + 1;
        }
    }

    /**
     * Create new map
     */
    public function mmm_create_new_map()
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'mapmarker_option', [
                'map_id' => $_GET['map_id'],
                'map_name' => __('Untitled map', 'map-multi-marker') . ' ' . $_GET['map_id'],
                'height_map' => MapMultiMarker::DEFAULT_HEIGHT_MAP,
                'height_valeur_type' => MapMultiMarker::DEFAULT_HEIGHT_VALUE_TYPE,
                'width_map' => MapMultiMarker::DEFAULT_WIDTH_MAP,
                'width_valeur_type' => MapMultiMarker::DEFAULT_WIDTH_VALUE_TYPE,
                'streetview' => MapMultiMarker::DEFAULT_STREET_VIEW,
                'maptype' => MapMultiMarker::DEFAULT_MAP_TYPE,
                'zoom' => MapMultiMarker::DEFAULT_ZOOM,
                'lightbox' => MapMultiMarker::DEFAULT_LIGHT_BOX,
                'scrollwheel' => MapMultiMarker::DEFAULT_SCROLL_WHEEL,
                'latitude_initial' => MapMultiMarker::DEFAULT_LATITUDE_INITIAL,
                'longitude_initial' => MapMultiMarker::DEFAULT_LONGITUDE_INITIAL,
                'fiels_to_display' => MapMultiMarker::DEFAULT_FIELD_TO_DISPLAY,
                'default_marker_img_url' => MapMultiMarker::DEFAULT_MARKER_IMG_ID,
                'default_desc_img_url' => MapMultiMarker::DEFAULT_DESC_IMG_ID,
            ]
        );
    }

    /**
     * Get all map id
     *
     * @return array|object|null
     */
    function mmm_get_all_map_id()
    {
        return $this->model->get_map_all_id();
    }

    /**
     * Create new marker
     */
    function mmm_create_marker()
    {

        global $wpdb;

        // Déclare la table
        $table_name = $wpdb->prefix . 'mapmarker_marker';

        // Supprime les antislashe pour eviter les bugs
        $_POST = stripslashes_deep($_POST);

        // Si les variable des champs envoyé n'existe pas
        if (!isset($_POST['add_titre'])) {
            $_POST['add_titre'] = '';
        }
        if (!isset($_POST['add_description'])) {
            $_POST['add_description'] = '';
        }
        if (!isset($_POST['add_adresse'])) {
            $_POST['add_adresse'] = '';
        }
        if (!isset($_POST['add_telephone'])) {
            $_POST['add_telephone'] = '';
        }
        if (!isset($_POST['add_weblink'])) {
            $_POST['add_weblink'] = '';
        }
        if (!isset($_POST['add_img_desc_marker'])) {
            $option = $wpdb->get_results("SELECT default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET['map_id'] . '"', ARRAY_A);
            $_POST['add_img_desc_marker'] = $option[0]['default_desc_img_url'];
        }

        // Req. insert sql
        $wpdb->insert($table_name, [
                'marker_id' => sanitize_text_field($_GET['map_id']),
                'titre' => sanitize_text_field($_POST['add_titre']),
                'description' => implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['add_description']))),
                'adresse' => sanitize_text_field($_POST['add_adresse']),
                'telephone' => sanitize_text_field($_POST['add_telephone']),
                'weblink' => sanitize_text_field($_POST['add_weblink']),
                'latitude' => sanitize_text_field($_POST['add_latitude']),
                'longitude' => sanitize_text_field($_POST['add_longitude']),
                'img_desc_marker' => sanitize_text_field($_POST['add_img_desc_marker']),
                'img_icon_marker' => sanitize_text_field($_POST['add_img_icon_marker'])
            ]
        );


    }

}