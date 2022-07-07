<?php
/*
Plugin Name: Map Multi Marker
Plugin URI: http://mickael.austoni.fr
Description: The easiest, useful and powerful google map plugin ! Easily create an unlimited number of google map and marker.
Version: 3.1.9
Author: Mickael Austoni
Author URI: http://mickael.austoni.fr
Text Domain: map-multi-marker
License: GPL2
*/


if (!defined('WPINC')) {
    die;
}

class MapMultiMarker
{
    const VERSION = '3.1.9';
    const TEXT_DOMAIN = 'map-multi-marker';
    const PLUGIN_NAME = 'Map Multi Marker';
    const DEFAULT_MAP_ID = 1;
    const DEFAULT_HEIGHT_MAP = 500;
    const DEFAULT_HEIGHT_VALUE_TYPE = 'px';
    const DEFAULT_WIDTH_MAP = 100;
    const DEFAULT_WIDTH_VALUE_TYPE = '%';
    const DEFAULT_STREET_VIEW = 0;
    const DEFAULT_MAP_TYPE = 'TERRAIN';
    const DEFAULT_ZOOM = 2;
    const DEFAULT_LIGHT_BOX = 0;
    const DEFAULT_SCROLL_WHEEL = 1;
    const DEFAULT_LATITUDE_INITIAL = 46.437857;
    const DEFAULT_LONGITUDE_INITIAL = 2.570801;
    const DEFAULT_FIELD_TO_DISPLAY = 'image,titre,description,adresse,telephone,weblink';
    const DEFAULT_API_KEY = 'AIzaSyCRC7476v-ecw7Cp_9xT-cjbJi75sQhdhM';
    const DEFAULT_DESC_IMG_URL = 'asset/img/desc-marker.jpg';
    const DEFAULT_DESC_IMG_ID = 0;
    const DEFAULT_MARKER_IMG_URL = 'asset/img/icon-marker.png';
    const DEFAULT_MARKER_IMG_ID = 0;
    const DONATE_LINK = 'https://www.paypal.me/0ze';
    /**
     * @var array
     */
    /**
     * @var string
     */
    public $plugin_url;
    /**
     * @var
     */
    public $plugin_path;
    /**
     * @var
     */
    public $page;
    /**
     * @var
     */
    public $isPluginPage;
    /**
     * @var
     */
    private $maps = [];

    /**
     * MapMultiMarker constructor.
     */
    public function __construct()
    {
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->page = isset($_GET['page']) ? $_GET['page'] : null;
        $this->isPluginPage = (strpos($this->page, self::TEXT_DOMAIN . '-page') === false) ? false : true;

        add_action('plugins_loaded', [$this, 'mmm_plugin_loaded']);
        add_action('wp_ajax_mmm_async_content_marker', [$this, 'mmm_async_content_marker']);
        add_action('wp_ajax_nopriv_mmm_async_content_marker', [$this, 'mmm_async_content_marker']);

        if (is_admin()) {
            if ($this->isPluginPage) {
                add_action('admin_enqueue_scripts', [$this, 'mmm_admin_script']);
            }
            register_activation_hook(__FILE__, [$this, 'mmm_plugin_install_db']);
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'mmm_admin_action_links']);
            add_action('admin_menu', [$this, 'mmm_admin_menu']);
            add_action('wp_ajax_mmm_async_modal_delete', [$this, 'mmm_async_modal_delete']);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'mmm_front_register_script']);
            add_shortcode(self::TEXT_DOMAIN, [$this, 'mmm_short_code']);

        }
    }

    /**
     * Plugin init
     */
    public function mmm_plugin_loaded()
    {
        load_plugin_textdomain(self::TEXT_DOMAIN, false, self::TEXT_DOMAIN . '/language');
        $this->mmm_plugin_upgrade();
    }

    /**
     * Plugin install db
     */
    public function mmm_plugin_install_db()
    {
        global $wpdb;

        //Load le 'upgrade.php' pour le call de la fonction dbDelta()
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        //Encodage
        $charset_collate = $wpdb->get_charset_collate();

        //Nom des tables
        $table_option = $wpdb->prefix . 'mapmarker_option';
        $table_marker = $wpdb->prefix . 'mapmarker_marker';
        $table_api = $wpdb->prefix . 'mapmarker_api';


        // Req. de création de table pour le "options"
        $sql1 = "CREATE TABLE IF NOT EXISTS $table_option (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            map_id INT(11) NOT NULL,
            map_name VARCHAR(255) NOT NULL,
            height_map INT(11) NOT NULL,
            height_valeur_type CHAR(2) NOT NULL,
            width_map INT(11) NOT NULL,
            width_valeur_type CHAR(2) NOT NULL,
            streetview INT(1) NOT NULL,
            maptype VARCHAR(50) DEFAULT '' NOT NULL,
            zoom INT(2) NOT NULL,
            lightbox INT(0) NOT NULL,
            scrollwheel INT(1) NOT NULL,
            latitude_initial DECIMAL(10, 8) NOT NULL,
            longitude_initial DECIMAL(11, 8) NOT NULL,
            fiels_to_display VARCHAR(50) NOT NULL,
            default_marker_img_url VARCHAR(255) DEFAULT '' NOT NULL,
            default_desc_img_url VARCHAR(255) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";
        // Call dbDelta pour la requete
        dbDelta($sql1);


        // Req de selection sql
        $check_option = $wpdb->get_results("SELECT * FROM " . $table_option, ARRAY_A);

        // Si la requete est vide on inseret les option par default
        if (empty($check_option)) {
            // Insert les valeur des options par defaut en bdd
            $wpdb->insert($table_option, [
                    'map_id' => '1',
                    'map_name' => __('Example map', self::TEXT_DOMAIN),
                    'height_map' => self::DEFAULT_HEIGHT_MAP,
                    'height_valeur_type' => self::DEFAULT_HEIGHT_VALUE_TYPE,
                    'width_map' => self::DEFAULT_WIDTH_MAP,
                    'width_valeur_type' => self::DEFAULT_WIDTH_VALUE_TYPE,
                    'streetview' => self::DEFAULT_STREET_VIEW,
                    'maptype' => self::DEFAULT_MAP_TYPE,
                    'zoom' => self::DEFAULT_ZOOM,
                    'lightbox' => self::DEFAULT_LIGHT_BOX,
                    'scrollwheel' => self::DEFAULT_SCROLL_WHEEL,
                    'latitude_initial' => self::DEFAULT_LATITUDE_INITIAL,
                    'longitude_initial' => self::DEFAULT_LONGITUDE_INITIAL,
                    'fiels_to_display' => self::DEFAULT_FIELD_TO_DISPLAY,
                    'default_marker_img_url' => self::DEFAULT_MARKER_IMG_ID,
                    'default_desc_img_url' => self::DEFAULT_DESC_IMG_ID,
                ]
            );
        }


        // Req. de création de table pour les "marqueurs"
        $sql2 = "CREATE TABLE IF NOT EXISTS $table_marker (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            marker_id INT(11) NOT NULL,
            titre VARCHAR(50) DEFAULT '' NOT NULL,
            description VARCHAR(255) DEFAULT '' NOT NULL,
            adresse VARCHAR(255) DEFAULT '' NOT NULL,
            telephone VARCHAR(20) DEFAULT '' NOT NULL,
            weblink VARCHAR(255) DEFAULT '' NOT NULL,
            img_desc_marker VARCHAR(255) DEFAULT '0' NOT NULL,
            img_icon_marker VARCHAR(255) DEFAULT '0' NOT NULL,
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            UNIQUE KEY id (id)
            ) $charset_collate;";
        // Call dbDelta pour la requete
        dbDelta($sql2);


        // Req de selection sql
        $check_marqueur = $wpdb->get_results("SELECT * FROM " . $table_marker, ARRAY_A);

        // Si la requete est vide on insert un marqueurs par default
        if (empty($check_marqueur)) {
            // Insert un marqueur par defaut en bdd
            $wpdb->insert($table_marker, [
                    'marker_id' => '1',
                    'titre' => __("Eiffel Tower", "map-multi-marker"),
                    'description' => __("Constructed in 1889 as the entrance to the 1889 World's Fair, it was initially criticized by some of France's leading artists and intellectuals for its design, but it has become a global cultural icon of France...",
                        "map-multi-marker"
                    ),
                    'adresse' => 'Champ de Mars, 5 Avenue Anatole',
                    'telephone' => '0123456789',
                    'weblink' => 'http://www.toureiffel.paris/en/home.html',
                    'img_desc_marker' => 0,
                    'img_icon_marker' => 0,
                    'latitude' => '48.8583701',
                    'longitude' => '2.2922926'
                ]
            );
        }

        // Req. de création de table pour l'API de google
        $sql3 = "CREATE TABLE IF NOT EXISTS $table_api (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    apikey VARCHAR(255) DEFAULT '' NOT NULL,
    language VARCHAR(10) DEFAULT '' NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
        // Call dbDelta pour la requete
        dbDelta($sql3);


        // Req de selection sql
        $check_apikey = $wpdb->get_results("SELECT apikey FROM " . $table_api, ARRAY_A);

        // Si la requete est vide on inseret les option par default
        if (empty($check_apikey)) {
            // Insert une valeur vide
            $wpdb->insert($table_api, [
                    'apikey' => '',
                    'language' => substr(get_locale(), 0, 2)
                ]
            );
        }

        //Ajoute les version d'upgrade
        update_option('map_multi_marker_add_table_2_1', true);
        update_option('map_multi_marker_add_table_2_2', true);
        update_option('map_multi_marker_add_table_2_3', true);
        update_option('map_multi_marker_add_table_2_4', true);
        update_option('map_multi_marker_add_table_2_9', true);
    }

    /**
     * Upgrade plugin
     */
    public function mmm_plugin_upgrade()
    {
        // Add current version in db
        if (!get_option('map_multi_marker_version') || get_option('map_multi_marker_version') !== self::VERSION) {
            update_option('map_multi_marker_version', self::VERSION);
        }

        // Upgrade 2.1
        if (!get_option('map_multi_marker_add_table_2_1')) {
            global $wpdb;
            $table_option = $wpdb->prefix . 'mapmarker_option';
            $table_marker = $wpdb->prefix . 'mapmarker_marker';
            $wpdb->query("ALTER TABLE $table_marker ADD marker_id VARCHAR(255) NOT NULL DEFAULT 1 AFTER id");
            $wpdb->query("ALTER TABLE $table_option ADD map_id INT(11) NOT NULL DEFAULT 1 AFTER id");
            $wpdb->query("ALTER TABLE $table_option ADD map_name VARCHAR(255) NOT NULL DEFAULT 'Example map' AFTER map_id");
            update_option('map_multi_marker_add_table_2_1', true);
        }

        // Upgrade 2.2
        if (!get_option('map_multi_marker_add_table_2_2')) {
            global $wpdb;
            $table_option = $wpdb->prefix . 'mapmarker_option';
            $wpdb->query("ALTER TABLE $table_option ADD scrollwheel INT(1) NOT NULL DEFAULT 1 AFTER zoom");
            update_option('map_multi_marker_add_table_2_2', true);
        }

        // Upgrade 2.3
        if (!get_option('map_multi_marker_add_table_2_3')) {
            global $wpdb;
            $table_option = $wpdb->prefix . 'mapmarker_option';
            $wpdb->query("ALTER TABLE $table_option ADD default_marker_img_url VARCHAR(255) DEFAULT '0' NOT NULL");
            $wpdb->query("ALTER TABLE $table_option ADD default_desc_img_url VARCHAR(255) DEFAULT '0' NOT NULL");
            update_option('map_multi_marker_add_table_2_3', true);
        }

        // Upgrade 2.4
        if (!get_option('map_multi_marker_add_table_2_4')) {
            global $wpdb;
            $table_option = $wpdb->prefix . 'mapmarker_option';
            $wpdb->query("ALTER TABLE $table_option ADD lightbox INT(1) NOT NULL DEFAULT 0 AFTER zoom");
            update_option('map_multi_marker_add_table_2_4', true);
        }

        // Upgrade 2.9
        if (!get_option('map_multi_marker_add_table_2_9')) {
            global $wpdb;
            $table_marker = $wpdb->prefix . 'mapmarker_marker';
            $wpdb->query("ALTER TABLE $table_marker CHANGE COLUMN `telephone` `telephone` VARCHAR(20) NOT NULL DEFAULT '' , CHANGE COLUMN `img_desc_marker` `img_desc_marker` VARCHAR(255) NOT NULL DEFAULT 0 ,CHANGE COLUMN `img_icon_marker` `img_icon_marker` VARCHAR(255) NOT NULL DEFAULT 0"
            );
            update_option('map_multi_marker_add_table_2_9', true);
        }
    }

    /**
     * Menu admin
     */
    public function mmm_admin_menu()
    {
        add_menu_page('Map Multi Marker', __('Map Multi Marker'), 'manage_options', 'map-multi-marker-page-manage', [$this, 'mmm_rooting'], 'dashicons-location-alt');

        add_submenu_page('map-multi-marker-page-manage', __('Settings Map Multi Marker', 'map-multi-marker'), __('Settings', 'map-multi-marker'), 'manage_options', 'map-multi-marker-page-settings',
            [$this, 'mmm_rooting']
        );

        add_submenu_page('map-multi-marker-page-manage', __('Help Map Multi Marker', 'map-multi-marker'), __('Help', 'map-multi-marker'), 'manage_options', 'map-multi-marker-page-help',
            [$this, 'mmm_rooting']
        );
    }

    /**
     * Admin action link
     *
     * @param $links
     *
     * @return array
     */
    public function mmm_admin_action_links($links)
    {
        $donate_link = ['<a style="font-weight:bold;color:#4CAF50;" href="' . self::DONATE_LINK . '" target="_blank">' . __('Donate', self::TEXT_DOMAIN) . '</a>'];

        return array_merge($links, $donate_link);
    }

    /**
     * Admin script
     */
    public function mmm_admin_script()
    {
        $localize = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'msg_error_empty' => __('Please choose a file.', self::TEXT_DOMAIN),
            'mssg_error_required' => __('Required fields.', self::TEXT_DOMAIN)
        ];

        wp_enqueue_style('fontawesome-css', $this->plugin_url . 'asset/css/font-awesome.min.css', false, MapMultiMarker::VERSION);
        wp_enqueue_style('mmm-admin-css', $this->plugin_url . 'asset/css/admin.css', false, MapMultiMarker::VERSION);
        wp_enqueue_script('bootstrap-min-js', $this->plugin_url . 'asset/js/bootstrap.min.js', ['jquery'], MapMultiMarker::VERSION, true);
        wp_enqueue_script('clipboard-js', $this->plugin_url . 'asset/js/clipboard.min.js', ['jquery'], MapMultiMarker::VERSION, true);
        wp_enqueue_script('mmm-admin-js', $this->plugin_url . 'asset/js/admin.js', ['jquery'], MapMultiMarker::VERSION, true);
        wp_localize_script('mmm-admin-js', 'localize', $localize);
        wp_enqueue_media();
    }

    /**
     * Front script
     */
    public function mmm_front_register_script()
    {
        require_once $this->plugin_path . 'app/model/model_mapper.php';
        $model = new model_mapper();
        $settings = $model->get_api_setting();

        wp_register_style('mmm-front-css', $this->plugin_url . 'asset/css/front.css', null, MapMultiMarker::VERSION);
        wp_register_style('fontawesome-css', $this->plugin_url . 'asset/css/font-awesome.min.css', null, MapMultiMarker::VERSION);
        wp_register_style('featherlight-css', $this->plugin_url . 'asset/css/featherlight.min.css', null, MapMultiMarker::VERSION);
        wp_register_script('mmm-googlemap-js', 'https://maps.googleapis.com/maps/api/js?key=' . $settings['api_key'] . '&language=' . $settings['language'], false, MapMultiMarker::VERSION, true);
        wp_register_script('mmm-front-js', $this->plugin_url . 'asset/js/front.js', null, MapMultiMarker::VERSION, true);
        wp_register_script('featherlight-js', $this->plugin_url . 'asset/js/featherlight.min.js', ['jquery'], MapMultiMarker::VERSION, true);
        wp_localize_script('mmm-front-js', 'localize', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    /**
     * Load
     */
    public function mmm_rooting()
    {

        if ($this->isPluginPage) {
            $controllerName = 'controller_' . str_replace('-', '_', $this->page);
            require_once $this->plugin_path . 'app/controller/' . $controllerName . '.php';
            //new $controllerName();
            new $controllerName();
        }
    }

    /**
     * Short code render
     *
     * @param $short_code_attribute
     *
     * @return false|string
     */
    public function mmm_short_code($short_code_attribute)
    {
        require_once $this->plugin_path . 'app/model/model_mapper.php';
        require_once $this->plugin_path . 'app/utility/map_multi_marker_utility.php';

        $model = new model_mapper();
        $map_id = intval($short_code_attribute['id']);
        $options = $model->get_map_options($map_id);
        $markers = $model->get_google_map_marker($map_id);
        $markers_and_options = [&$options, &$markers];

        // If map exist
        if ($options) {
            // Prepare array for google API
            map_multi_marker_utility::mmm_prepare_array_options($options);
            map_multi_marker_utility::mmm_prepare_array_markers($markers);

            // Force type
            array_walk_recursive($markers_and_options, ['map_multi_marker_utility', 'mmm_force_type']);

            // Store all map data
            $map = [
                'map_id' => $map_id,
                'map_option' => $options,
                'markers' => $markers
            ];

            // Set maps private var
            array_push($this->maps, $map);

            // Enqueue script
            wp_enqueue_script('mmm-googlemap-js');
            wp_enqueue_script('mmm-front-js');
            wp_enqueue_style('mmm-front-css');
            wp_enqueue_style('fontawesome-css');
            wp_localize_script('mmm-front-js', 'maps', $this->maps);


            // Optional script load
            if ($options['lightbox'] === '1') {
                wp_enqueue_style('featherlight-css');
                wp_enqueue_script('featherlight-js');
            }

            // Shortcode render
            if (shortcode_exists(self::TEXT_DOMAIN)) {
                ob_start();
                echo '<div id="map-multi-marker-' . $map_id . '" class="map-multi-marker" style="height: ' . $options['height_map'] . $options['height_valeur_type'] . '; width: ' . $options['width_map'] . $options['width_valeur_type'] . '"></div>';

                return ob_get_clean();
            }
        }

        return false;
    }

    /**
     * Get content marker
     */
    public function mmm_async_content_marker()
    {
        global $wpdb;
        require_once $this->plugin_path . 'app/utility/map_multi_marker_utility.php';

        $map_id = $wpdb->get_results("SELECT marker_id FROM " . $wpdb->prefix . 'mapmarker_marker WHERE id="' . $_POST["id"] . '"', ARRAY_A)[0];
        $option = $wpdb->get_results("SELECT fiels_to_display, lightbox FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $map_id['marker_id'] . '"', ARRAY_A)[0];
        $data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE id="' . $_POST['id'] . '"', ARRAY_A)[0];


        // If filed image is checked
        if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'image')) {
            // if lightbox is checked
            if ($option['lightbox'] == 1) {
                ?>
                <a href="<?php echo map_multi_marker_utility::mmm_get_image_desc_src($data['img_desc_marker'], 'large') ?>"
                   data-featherlight="image">
                    <img
                            src="<?php echo map_multi_marker_utility::mmm_get_image_desc_src($data['img_desc_marker']) ?>"
                            alt=""
                            class="img-in-marqueur <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'image') == true AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'],
                                    'titre'
                                ) == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'description') == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'],
                                    'adresse'
                                ) == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'telephone') == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'weblink'
                                ) == false) {
                                echo "full-width-desc";
                            } ?>">
                </a>
                <?php
            } else {
                ?>
                <img src="<?php echo map_multi_marker_utility::mmm_get_image_desc_src($data['img_desc_marker']) ?>"
                     alt=""
                     class="img-in-marqueur <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'image') == true AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'titre'
                         ) == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'description') == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'adresse'
                         ) == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'telephone') == false AND map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'weblink'
                         ) == false) {
                         echo "full-width-desc";
                     } ?>">
                <?php
            }
        }


        // If at least one filed is checked in description
        if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'titre') || map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'description'
            ) || map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'adresse') || map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'telephone'
            ) || map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'weblink')) {
            ?>

            <!-- Description des marqueurs -->
            <div class="wrap-desc-markeur <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'image') == false) {
                echo "full-width-desc-markeur";
            } ?>">
                <!-- Si le champ du "titre" est coché -->
                <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'titre') == true AND !empty($data['titre'])): ?>
                    <h2><?php echo html_entity_decode(esc_html($data['titre'])) ?></h2>
                <?php endif ?>

                <!-- Si le champ de la "description" est coché -->
                <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'description') == true AND !empty($data['description'])): ?>
                    <p class="description-marker"><?php echo html_entity_decode(esc_textarea($data['description'])) ?></p>
                <?php endif ?>

                <!-- Si le champ de la "adresse" ou "telephone" est coché -->
                <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'adresse') == true OR map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'telephone'
                    ) == true OR map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'weblink') == true): ?>
                    <ul class="contact-list">
                        <!-- Si le champ "adresse" est coché -->
                        <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'adresse') == true AND !empty($data['adresse'])): ?>
                            <li class="adresse">
                                <strong><?php echo html_entity_decode(esc_html($data['adresse'])) ?></strong></li>
                        <?php endif ?>
                        <!-- Si le champ de la "description" est coché -->
                        <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'telephone') == true AND !empty($data['telephone'])): ?>
                            <li class="telephone"><strong>
                                    <a
                                            href="tel:<?php echo str_replace(' ', '', esc_html($data['telephone'])) ?>"><?php echo esc_html($data['telephone'], 2, ' ') ?></a>
                                </strong>
                            </li>
                        <?php endif ?>
                        <!-- Si le champ du "lien" est coché -->
                        <?php if (map_multi_marker_utility::mmm_is_checked_field($option['fiels_to_display'], 'weblink') == true AND !empty($data['weblink'])): ?>
                            <li class="weblink"><strong>
                                    <a href="<?php echo esc_html($data['weblink']) ?>"
                                       target="_blank"><?php echo $data['weblink'] ?></a>
                                </strong>
                            </li>
                        <?php endif ?>
                    </ul>
                <?php endif ?>
            </div>
            <?php
        }

        wp_die();
    }

    /**
     * Alert message
     *
     * @param $message
     * @param $type
     */
    function mmm_alert_message($message, $type)
    {
        // Add_settings_error( $setting, $code, $message, $type )
        add_settings_error('mmm_message_alert', '', $message, $type);

        // Display
        settings_errors('mmm_message_alert');
    }

    /**
     * Modal delete
     */
    public function mmm_async_modal_delete()
    {
        //Si le modal est une suppression de map
        if ($_POST['modal'] == 'delete_map') {
            ?>
            <div class="wrap-modal-parent">
                <div class="wrap-modal-child">
                    <div class="modal-confirm">
                        <form action="" method="post">
                            <input type="hidden" name="securite_nonce_delete_map"
                                   value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                            <input type="hidden" name="id_delete_map" value="<?php echo $_POST['id'] ?>">
                            <h4><?php _e('Do you confirm the suppression of the map ?', 'map-multi-marker'); ?></h4>
                            <button id="cancel-supp"
                                    class="button-secondary"><?php _e('Cancel', 'map-multi-marker'); ?></button>
                            <button type="submit" class="button-primary" name="submit_delete"
                                    id="submit_delete"><?php _e('Confirm', 'map-multi-marker'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        //Si le modal est une suppression de marker
        if ($_POST['modal'] == 'delete_marker') {
            ?>
            <div class="wrap-modal-parent">
                <div class="wrap-modal-child">
                    <div class="modal-confirm">
                        <form action="" method="post">
                            <input type="hidden" name="securite_nonce_delete_marker"
                                   value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                            <input type="hidden" name="id_delete_marker" value="<?php echo $_POST['id'] ?>">
                            <h4><?php _e('Do you confirm the suppression of the marker ?', 'map-multi-marker'); ?></h4>
                            <button id="cancel-supp"
                                    class="button-secondary"><?php _e('Cancel', 'map-multi-marker'); ?></button>
                            <button type="submit" class="button-primary" name="submit_delete"
                                    id="submit_delete"><?php _e('Confirm', 'map-multi-marker'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Singleton
     *
     * @return MapMultiMarker|null
     */
    public static function instance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self;
        }

        return $instance;
    }
}

MapMultiMarker::instance();


