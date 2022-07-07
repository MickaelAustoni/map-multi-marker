<?php

require_once MapMultiMarker::instance()->plugin_path . 'app/controller/controller_map_multi_marker.php';

class controller_map_multi_marker_page_help extends controller_map_multi_marker
{

    /**
     * controller_map_multi_marker_page_help constructor.
     */
    public function __construct()
    {
        $this->loadView();

    }

}