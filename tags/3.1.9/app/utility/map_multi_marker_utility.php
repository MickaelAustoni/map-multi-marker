<?php

class map_multi_marker_utility
{
    /**
     * Force type var for google map api
     * @param $value
     * @param $key
     */
    static public function mmm_force_type(&$value, $key)
    {

        switch ($key) {

            case "maptype";
            case "mapTypeId";
                $value = strtolower($value);
                break;

            case "streetview";
            case "streetViewControl";
            case "scrollwheel";
                $value = boolval($value);
                break;

            case "id";
            case "zoom";
                $value = intval($value);
                break;

            case "lat";
            case "latitude";
            case "latitude_initial";
                $value = floatval($value);
                break;

            case "lng";
            case "longitude";
            case "longitude_initial";
                $value = floatval($value);
                break;

        }
    }

    /**
     * Prepare array options for google api
     * @param $options
     */
    static public function mmm_prepare_array_options(&$options)
    {

        foreach ($options as $key => $value) {

            switch ($key) {

                case "maptype";
                    $options['mapTypeId'] = $value;
                    unset($options['maptype']);
                    break;

                case "streetview";
                    $options['streetViewControl'] = $value;
                    unset($options['streetview']);
                    break;
            }
        }

        $options['center'] = ['lat' => $options['latitude_initial'], 'lng' => $options['longitude_initial']];
        unset($options['latitude_initial'], $options['longitude_initial']);

    }

    /**
     * Prepare array markers for google api
     * @param $markers
     */
    static public function mmm_prepare_array_markers(&$markers)
    {

        foreach ($markers as &$marker) {

            foreach ($marker as $key => $value) {

                // Change key of marker array
                switch ($key) {

                    case "latitude";
                        $marker['lat'] = $value;
                        unset($marker['latitude']);
                        break;

                    case "longitude";
                        $marker['lng'] = $value;
                        unset($marker['longitude']);
                        break;

                    case "img_icon_marker";
                        $marker['icon'] = self::mmm_get_image_marker_src($value);
                        unset($marker['img_icon_marker']);
                        break;
                }
            }
        }

    }

    /**
     * Get image description
     * @param      $id
     * @param null $size
     * @return string
     */
    static public function mmm_get_image_desc_src($id, $size = null)
    {

        if (is_numeric($id) AND $id !== '0') {
            if ($size === null) {
                $url = wp_get_attachment_image_src($id)[0];

                return $url;
            } else {
                $url = wp_get_attachment_image_src($id, $size)[0];

                return $url;
            }
        } else {
            if ($id === '0') {

                return MapMultiMarker::instance()->plugin_url . MapMultiMarker::DEFAULT_DESC_IMG_URL;
            } else {
                return $id;
            }
        }
    }

    /**
     * Get image marker
     * @param $id
     * @return false|string
     */
    static public function mmm_get_image_marker_src($id)
    {
        if (is_numeric($id) AND $id !== '0') {
            return wp_get_attachment_url($id);

        } else {
            if ($id === '0') {
                return MapMultiMarker::instance()->plugin_url . MapMultiMarker::DEFAULT_MARKER_IMG_URL;

            } else {
                return $id;
            }
        }
    }

    /**
     * Check if filed is checked
     * @param $str
     * @param $findMe
     * @return bool
     */
    static public function mmm_is_checked_field($str, $findMe)
    {
        $pos = strpos($str, $findMe);

        if ($pos !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if filed is checked echo
     * @param $str
     * @param $findme
     */
    static function mmm_e_is_checked_field($str, $findme)
    {
        // Recherche la chaine($findme) dans une chaine($str)
        $pos = strpos($str, $findme);

        // Si la chaine est trouvé, on coche le selecteur
        if ($pos !== FALSE) {
            echo 'checked';
        }
    }
}