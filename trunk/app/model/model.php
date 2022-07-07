<?php

namespace map_multi_marker\model;

class model
{
    /**
     * @var
     */
    public $api_settings
        = [
            'api_key' => '',
            'language' => ''
        ];
    /**
     * @var
     */
    public $marker
        = [
            'marker_id' => '',
            'title' => '',
            'description' => '',
            'address' => '',
            'tel' => '',
            'web_link' => '',
            'img_desc_marker' => '',
            'img_icon_marker' => '',
            'latitude' => '',
            'longitude' => ''
        ];
    /**
     * @var
     */
    public $map_options
        = [
            'map_id' => '',
            'map_name' => '',
            'map_type' => '',
            'height_map' => '',
            'height_value_type' => '',
            'width_map' => '',
            'width_value_type' => '',
            'street_view' => '',
            'zoom' => '',
            'light_box' => '',
            'scroll_wheel' => '',
            'latitude_initial' => '',
            'longitude_initial' => '',
            'field_to_display' => '',
            'default_marker_img_url' => '',
            'default_desc_img_url' => '',
        ];

    //======================================================================
    // API SETTINGS
    //======================================================================

    /**
     * @return mixed
     */
    public function get_language()
    {
        return $this->api_settings['language'];
    }

    /**
     * @param $language
     */
    public function set_language($language)
    {
        $this->api_settings['language'] = $language;
    }

    /**
     * @return mixed
     */
    public function get_api_key()
    {
        return $this->api_settings['api_key'];
    }

    /**
     * @param $language
     */
    public function set_api_key($language)
    {
        $this->api_settings['api_key'] = $language;
    }

    //======================================================================
    // MAP MARKER
    //======================================================================

    /**
     * @return mixed
     */
    public function get_marker_id()
    {
        return $this->marker['marker_id'];
    }

    /**
     * @param $marker_id
     */
    public function set_marker_id($marker_id)
    {
        $this->marker['marker_id'] = $marker_id;
    }

    /**
     * @return mixed
     */
    public function get_title()
    {
        return $this->marker['title'];
    }

    /**
     * @param $title
     */
    public function set_title($title)
    {
        $this->marker['api_key'] = $title;
    }

    /**
     * @return mixed
     */
    public function get_description()
    {
        return $this->marker['description'];
    }

    /**
     * @param $description
     */
    public function set_description($description)
    {
        $this->marker['description'] = $description;
    }

    /**
     * @return mixed
     */
    public function get_address()
    {
        return $this->marker['address'];
    }

    /**
     * @param $address
     */
    public function set_address($address)
    {
        $this->marker['address'] = $address;
    }

    /**
     * @return mixed
     */
    public function get_tel()
    {
        return $this->marker['tel'];
    }

    /**
     * @param $tel
     */
    public function set_tel($tel)
    {
        $this->marker['tel'] = $tel;
    }

    /**
     * @return mixed
     */
    public function get_web_link()
    {
        return $this->marker['web_link'];
    }

    /**
     * @param $web_link
     */
    public function set_web_link($web_link)
    {
        $this->marker['web_link'] = $web_link;
    }

    /**
     * @return mixed
     */
    public function get_img_desc_marker()
    {
        return $this->marker['img_desc_marker'];
    }

    /**
     * @param $img_desc_marker
     */
    public function set_img_desc_marker($img_desc_marker)
    {
        $this->marker['img_desc_marker'] = $img_desc_marker;
    }

    /**
     * @return mixed
     */
    public function get_img_icon_marker()
    {
        return $this->marker['img_icon_marker'];
    }

    /**
     * @param $img_icon_marker
     */
    public function set_img_icon_marker($img_icon_marker)
    {
        $this->marker['img_icon_marker'] = $img_icon_marker;
    }

    /**
     * @return mixed
     */
    public function get_latitude()
    {
        return $this->marker['latitude'];
    }

    /**
     * @param $latitude
     */
    public function set_latitude($latitude)
    {
        $this->marker['latitude'] = $latitude;
    }

    /**
     * @return mixed
     */
    public function get_longitude()
    {
        return $this->marker['longitude'];
    }

    /**
     * @param $longitude
     */
    public function set_longitude($longitude)
    {
        $this->marker['longitude'] = $longitude;
    }

    //======================================================================
    // MAP OPTIONS
    //======================================================================

    /**
     * @return mixed
     */
    public function get_map_id()
    {
        return $this->map_options['map_id'];
    }

    /**
     * @param $map_id
     */
    public function set_map_id($map_id)
    {
        $this->map_options['map_id'] = $map_id;
    }

    /**
     * @return mixed
     */
    public function get_map_name()
    {
        return $this->map_options['map_name'];
    }

    /**
     * @param $map_name
     */
    public function set_map_name($map_name)
    {
        $this->map_options['map_name'] = $map_name;
    }

    /**
     * @return mixed
     */
    public function get_map_type()
    {
        return $this->map_options['map_type'];
    }

    /**
     * @param $map_type
     */
    public function set_map_type($map_type)
    {
        $this->map_options['map_type'] = $map_type;
    }

    /**
     * @return mixed
     */
    public function get_height_map()
    {
        return $this->map_options['height_map'];
    }

    /**
     * @param $height_map
     */
    public function set_height_map($height_map)
    {
        $this->map_options['height_map'] = $height_map;
    }

    /**
     * @return mixed
     */
    public function get_height_value_type()
    {
        return $this->map_options['height_value_type'];
    }

    /**
     * @param $height_value_type
     */
    public function set_height_value_type($height_value_type)
    {
        $this->map_options['height_value_type'] = $height_value_type;
    }

    /**
     * @return mixed
     */
    public function get_width_map()
    {
        return $this->map_options['width_map'];
    }

    /**
     * @param $width_map
     */
    public function set_width_map($width_map)
    {
        $this->map_options['width_map'] = $width_map;
    }

    /**
     * @return mixed
     */
    public function get_width_value_type()
    {
        return $this->map_options['width_value_type'];
    }

    /**
     * @param $width_value_type
     */
    public function set_width_value_type($width_value_type)
    {
        $this->map_options['width_value_type'] = $width_value_type;
    }

    /**
     * @return mixed
     */
    public function get_street_view()
    {
        return $this->map_options['street_view'];
    }

    /**
     * @param $street_view
     */
    public function set_street_view($street_view)
    {
        $this->map_options['street_view'] = $street_view;
    }

    /**
     * @return mixed
     */
    public function get_zoom()
    {
        return $this->map_options['zoom'];
    }

    /**
     * @param $zoom
     */
    public function set_zoom($zoom)
    {
        $this->map_options['zoom'] = $zoom;
    }

    /**
     * @return mixed
     */
    public function get_light_box()
    {
        return $this->map_options['light_box'];
    }

    /**
     * @param $light_box
     */
    public function set_light_box($light_box)
    {
        $this->map_options['light_box'] = $light_box;
    }

    /**
     * @return mixed
     */
    public function get_scroll_wheel()
    {
        return $this->map_options['scroll_wheel'];
    }

    /**
     * @param $scroll_wheel
     */
    public function set_scroll_wheel($scroll_wheel)
    {
        $this->map_options['scroll_wheel'] = $scroll_wheel;
    }

    /**
     * @return mixed
     */
    public function get_latitude_initial()
    {
        return $this->map_options['latitude_initial'];
    }

    /**
     * @param $latitude_initial
     */
    public function set_latitude_initial($latitude_initial)
    {
        $this->map_options['latitude_initial'] = $latitude_initial;
    }

    /**
     * @return mixed
     */
    public function get_longitude_initial()
    {
        return $this->map_options['longitude_initial'];
    }

    /**
     * @param $longitude_initial
     */
    public function set_longitude_initial($longitude_initial)
    {
        $this->map_options['longitude_initial'] = $longitude_initial;
    }

    /**
     * @return mixed
     */
    public function get_field_to_display()
    {
        return $this->map_options['field_to_display'];
    }

    /**
     * @param $field_to_display
     */
    public function set_field_to_display($field_to_display)
    {
        $this->map_options['field_to_display'] = $field_to_display;
    }

    /**
     * @return mixed
     */
    public function get_default_marker_img_url()
    {
        return $this->map_options['default_marker_img_url'];
    }

    /**
     * @param $default_marker_img_url
     */
    public function set_default_marker_img_url($default_marker_img_url)
    {
        $this->map_options['default_marker_img_url'] = $default_marker_img_url;
    }

    /**
     * @return mixed
     */
    public function get_default_desc_img_url()
    {
        return $this->map_options['default_desc_img_url'];
    }

    /**
     * @param $default_desc_img_url
     */
    public function set_default_desc_img_url($default_desc_img_url)
    {
        $this->map_options['default_desc_img_url'] = $default_desc_img_url;
    }

}